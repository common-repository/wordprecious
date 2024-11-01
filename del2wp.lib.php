<?php
/**
Title: WordPre.cio.us
Author: Chris Heisel
E-mail: del2wp@heisel.org
Version: 1.0
**/

function cmh_date_test( $entry) {
    $now_s = date('Y-m-d H:i:s');
    echo "<br><br>";
    echo "Current time on server: $now_s<br>";
    
    echo "Entry times:<br>";
    echo "RSS time: $entry->rss_date<br>";
    echo "Parsed/epoch time: $entry->date<br>";
    echo "Date for db: ".date('Y-m-d H:i:s', $entry->date);
}

function cmh_parse_w3cdtf ( $date_str, $offset_hours = False) {
	
	# regex to match wc3dtf
	$pat = "/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(:(\d{2}))?(?:([-+])(\d{2}):?(\d{2})|(Z))?/";
	
	if ( preg_match( $pat, $date_str, $match ) ) {
		list( $year, $month, $day, $hours, $minutes, $seconds) = 
			array( $match[1], $match[2], $match[3], $match[4], $match[5], $match[6]);
		
		# calc epoch for current date assuming GMT
		$epoch = strtotime("$year-$month-$day $hours:$minutes$seconds");

        if($offset_hours) {
		    $offset = $offset_hours * 60 * 60;
			$epoch = $epoch + $offset;
        }
		return $epoch;
	}
	else {
		return -1;
	}
}

class WordPressImporter {
    //Importer that inserts or updates entry in WP db (update if title / date found...)
    function WordPressImporter($wpdb) {
        $this->db = $wpdb;
		$this->dbposts = $this->db->posts;
		$this->dbcats = $this->db->categories;
		$this->dbpost2cat = $this->db->post2cat;
		$this->keys = $ENTRY_KEYS;
		$this->keys_to_sanitize = array('post_title', 'post_content', 'excerpt');
    } //end __init__()

    function import_post($entry, $check_dates = False) {
	    if($check_dates) {
		    $post_id = $this->findID($entry['post_title'], $entry['post_date']);
		}
		else {
		    $post_id = $this->findID($entry['post_title'], False);
		}
		
        if($post_id) {
            if(!array_key_exists('ID', $entry)) {
                $entry['ID'] = $post_id;
            }
            $this->updateEntry($entry);
            //echo "Update ".$entry['post_title'].": ".$entry['ID']."\n";
        }
        else {
           $entry['ID'] = $this->newEntry($entry);
           echo "Inserted ".$entry['post_title'].": ".$entry['ID']."\n";
        }
    } //end import_post()
    
    function updateEntry($entry) {
        $entry = $this->sanitize_entry($entry);
        wp_update_post($entry);
        $this->updateMeta($entry['ID'], $entry['meta']);
        return True;
    } //end updateEntry()
    
    function sanitize_entry($entry) {
        foreach($this->keys_to_sanitize as $key) {
            $entry[$key] = $this->db->escape($entry[$key]);
        }
        return $entry;
    } //end sanitize_entry()
    
    function newEntry($entry) {
        $entry = $this->sanitize_entry($entry);
        $post_id = wp_insert_post($entry);
        $this->newMeta($post_id, $entry['meta']);
        return $post_id;
    } //end newEntry()

    function newMeta($post_id, $meta, $unique = False) {
        foreach($meta as $key => $value) {
            $key = $this->db->escape($key);
            $value = $this->db->escape($value);
            //echo "Adding: $key => $value <br>\n";
            add_post_meta($post_id, $key, $value, $unique);
        }
    } //end newMeta()
    
    function updateMeta($post_id, $meta) {
        foreach($meta as $key => $value) {
            $key = $this->db->escape($key);
            $value = $this->db->escape($value);
            //echo "Updating: $key => $value <br>\n";
            update_post_meta($post_id, $key, $value);
        }
    } //end updateMeta()
    
    function findID($post_title, $post_date = False) {
		$post_title = $this->db->escape($post_title);
		if($post_date) {
			$post_date = $this->db->escape($post_date);
			$query = "SELECT ID FROM ".$this->dbposts." WHERE post_title = '".$post_title."' AND post_date = '".$post_date."'";
		}
		else {
			$query = "SELECT ID FROM ".$this->dbposts." WHERE post_title = '".$post_title."'";
		}
		$id = $this->db->get_var($query);
		return $id;
    } //end findID()

}
?>
<?php
//Blogmark classes
class Blogmark {
    function Blogmark() {
        $this->title = '';
        $this->url = '';
        $this->description = '';
        $this->date = '';
    }
    
    function repr() {
    	if(!isset($this->asString)) {
            $this->asString = '<p class="link"><a href="'.$this->url.'">'.$this->title.'</a></p><p class="extended">'.$this->description.'</p>';
    	}
    	return $this->asString;
    }
    
    function wpMarshall($defaults) {
        $entry = array();
        $entry['post_author'] = $defaults['author'];
        $entry['post_title'] = $this->title;
        $entry['post_date'] = date('Y-m-d H:i:s', $this->date);
        $entry['post_content'] =$this->repr();
        $entry['excerpt'] = $this->repr();
        $entry['post_title'] = $this->title;
        $entry['post_status'] = 'publish';
        $entry['comment_status'] = $defaults['comment_status'];
        $entry['ping_status'] = $defaults['ping_status'];
        
        $post_meta = array();
        $post_meta['blogmark_url'] = $this->url;
        $post_meta['blogmark_description'] = $this->description;
        $entry['meta'] = $post_meta;
        
        $categories = $defaults['categories'];
        $entry['post_category'] = $categories;
        
        return $entry;
    }
}

class BlogmarkFactory {
    function fromRSS($rss, $offset_hours = False) {
        $feed=$rss;
        $collection=array();
        
        foreach($feed->items as $item) {
            $blogmark = new Blogmark;
            $blogmark->title = $item['title'];
            $blogmark->url = $item['link'];
            $blogmark->description = $item['description'];
            $blogmark->date = cmh_parse_w3cdtf($item['dc']['date'], $offset_hours);
            $blogmark->rss_date = $item['dc']['date'];
            $collection[]=$blogmark;
        } 
        
        return $collection;
    }
}
?>
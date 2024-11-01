require "ftools"
require "date"

PROJNAME="wordprecious"

REPO="http://svn.wp-plugins.org/#{PROJNAME}"
TAG="release-1.01"

desc "Exports a copy of a tag to build/"
task :export => [:clean_build] do |t|
  tag = ENV["tag"] || TAG
  cmd = "svn export #{REPO}/tags/#{tag} build/#{tag}"
  docmd(cmd)
  ENV["tag"] = tag
end

desc "Builds #{TAG}, removes non-dist files and tar and gzips."
task :release_tag => [:export, :clean_dist] do |t|
  tag = ENV["tag"] || TAG
  build = "build/#{tag}"
 
  puts tag, build
  release_prep(build, tag)
end

desc "Builds the trunk, removes non-dist files and tar and gzips."
task :release_trunk => [:clean_dist, :trunk] do |t|
    tag = ENV["tag"]
    build = "build/#{tag}"
    
    release_prep(build, tag)
end

desc "Exports the trunk to build/trunk-date"
task :trunk => [:svn_status, :clean_build ] do |t|
  now = DateTime.now()
  datestr = "#{now.year}-#{now.month}-#{now.day}_#{now.hour}-#{now.min}"
  ENV["tag"] = "trunk_#{datestr}"
  cmd = "svn export #{REPO}/trunk build/trunk_#{datestr}"
  docmd(cmd)
end

desc "Deletes the build directories"
task :clean => ["build/", "dist/"] do |t|
  clean()
end

desc "Deletes all files inside the dist directory."
task :clean_dist => ["dist/"] do |t|
    sh "rm -rf dist/*"
end    

desc "Deletes all folders inside the build directory"
task :clean_build => ["build/"] do |t|
  sh "rm -rf build/*"
end

desc "Makes a folder to export to."
directory "build/"

desc "Makes a folder for the zip, tar.gz"
directory "dist/"

desc "Uploads current code to heisel.org for testing"
task :upload do |t|
  cmd = "rsync --exclude=\".DS_Store\" --exclude=\"build\" --exclude=\"cache\" -ave ssh ./ cmheisel@heisel.org:sites/shakedown.heisel.org/scripts/del2wp/"
  docmd(cmd)
end

desc "Test to make sure the app is in SVN OK"
task :svn_status do |t|
  cmd = IO.popen("svn status | grep html")
  while line = cmd.gets
    if not line.empty?
      puts line
      return false
    end
  end
  cmd = IO.popen("svn status | grep css")
  while line = cmd.gets
    if not line.empty?
      puts line
      return false
    end
  end
  cmd = IO.popen("svn status | grep js")
  while line = cmd.gets
    if not line.empty?
      puts line
      return false
    end
  end
  cmd = IO.popen("svn status | grep jpg")
  while line = cmd.gets
    if not line.empty?
      puts line
      return false
    end
  end
  cmd = IO.popen("svn status | grep gif")
  while line = cmd.gets
    if not line.empty?
      puts line
      return false
    end
  end
  cmd = IO.popen("svn status | grep png")
  while line = cmd.gets
    if not line.empty?
      puts line
      return false
    end
  end
  cmd = IO.popen("svn status | grep php")
  while line = cmd.gets
    if not line.empty?
      puts line
      return false
    end
  end
end

def release_prep(build, tag)
    cmd = "rm -rf #{build}/lib"
    docmd(cmd)
    
    cmd = "rm #{build}/Rakefile"
    docmd(cmd)
    
    cmd = "rm #{build}/del2wp.config.php"
    docmd(cmd)
    
    cmd = "cd #{build}/ && tar -cvzf ../../dist/wordprecious-#{tag}.tar.gz ./*"
    docmd(cmd)
    
    cmd = "cd #{build}/ && zip ../../dist/wordprecious-#{tag}.zip ./*"
    docmd(cmd)
end    

def docmd(cmdstr)
  cmd = IO.popen(cmdstr, "r")
  while line = cmd.gets
    puts line
  end
  cmd.close
end

def clean
    sh "rm -rf build"
    sh "rm -rf dist"
end
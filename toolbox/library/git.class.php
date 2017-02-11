<?php
namespace toolbox;

class git {

    private $path_to_git = 'git';
    function setPathToGit($path){
        $this->path_to_git = $path;
    }

    private $branch = null;
    function getCurrentBranch(){
        if($this->branch === null){
            $output = array();
            exec($this->path_to_git." rev-parse --abbrev-ref HEAD", $output);
            $this->branch = $output[0];
        }

        return $this->branch;

    }

    function getLastUpdateTime($branch = null){

        if($branch === null){
            $branch = $this->getCurrentBranch();
        }

        $output = array();
        exec($this->path_to_git." log $branch -1 --format=format:%ci", $output);
        return $output[0];

    }

    function getChanges($start = 0, $limit = 10){
        $output = array();
        $cmd = $this->path_to_git." log -n ".$limit." --skip=".$start." --pretty=format:\"%h>><<%an>><<%aD>><<%s\"";
        exec($cmd, $output);
        $data = array();
        foreach($output as $commit){
            $commit_hash = substr($commit, 0, strpos($commit, '>><<'));
            $commit = substr($commit, strpos($commit, '>><<')+4);
            $commit_author = substr($commit, 0, strpos($commit, '>><<'));
            $commit = substr($commit, strpos($commit, '>><<')+4);
            $commit_date= substr($commit, 0, strpos($commit, '>><<'));
            $commit = substr($commit, strpos($commit, '>><<')+4);
            $commit_msg = $commit;

            $data[$commit_hash] = array(
                'hash' => $commit_hash,
                'msg' => $commit_msg,
                'author' => $commit_author,
                'date' => $commit_date,
                'files' => $this->getChangesByCommit($commit_hash)
            );

        }

        return $data;

    }

    function getChangesByCommit($commit){
        $output = array();
        $cmd = $this->path_to_git." log ".$commit." --numstat -n 1 --oneline";
        exec($cmd, $output);
        $data = array();
        foreach($output as $key => $line){
            if($key === 0){
                continue;
            }
            if(utils::stringStartsWith($line, 'warning:')){

                continue;
            }
            $parts = explode("\t", $line);
            if(count($parts) !== 3){
                continue;
            }
            $data[] = array(
                'file' => $parts[2],
                'inserts' => $parts[0],
                'deletes' => $parts[1],
            );


        }


        return $data;
    }


    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create($name = 'singleton'){
        self::$instances[$name] = new self($name);

        return self::$instances[$name];
        return new git();
    }

    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    public static $instances = array();
    public static function get($name = 'singleton'){
        if(!isset(self::$instances[$name])){
            self::$instances[$name] = new self($name);
        }
        return self::$instances[$name];
        return new git();
    }

}
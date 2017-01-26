<?
Class Logger{
  private $filename;
  private $logfile;

  public function Logger($filename){
    $this->filename = $filename;
    $this->logfile = fopen($this->filename, "a+");
  }

  public function dispose(){
    fclose($this->logfile);
    unset($this->logfile);
  }

  public function log($string){
    fputs($this->logfile, $string);
    fputs($this->logfile, "\r\n");
  }
}
?>

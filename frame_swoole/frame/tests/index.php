<?php

class Index{
  public function demo1(){
      echo "dmo1";
  }

    public function demo()
    {
        self::demo1();
  }

}

class Index2{
    public function demo2(){
        return "demo2";
    }
}

(new Index)->demo();

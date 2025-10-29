<?php 


namespace Lovillela\BlogApp\Services;

class DatabaseQueryService{

  private $connection;
  public function __construct($connection){
    $this->connection = $connection;
  }

  public function credentialCheck($username, $password) {
    /*In general terms
    hash the password
    $this->connection->query username and password (hashed)
    if correct, allows access (perhaps with a authorization service?)
    if not throw error
    */
  }

  private function genericQuery(string $query)  {
    /*perhaps some function to abstract queries?*/
  }
}
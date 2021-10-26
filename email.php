<?php
$conn = @mysqli_connect('localhost', 'root', '', 'sales');
if ($conn) {
    $masterEmail = isset($_REQUEST['email']) && $_REQUEST['email']
        ? $_REQUEST['email']
        : (
        isset($_REQUEST['masterEmail']) && $_REQUEST['masterEmail']
            ? $_REQUEST['masterEmail'] : false
        );
    if ($masterEmail) {
        $masterEmail = mysqli_real_escape_string($conn, $masterEmail);
        echo 'The master email is ' . $masterEmail . '\n';
        $sql = "SELECT * FROM users WHERE email='{$masterEmail}'";
        $res = mysqli_query($conn, $sql);
        if ($res && $res->num_rows > 0) {
            $row = mysqli_fetch_assoc($res);
            echo $row['username'] . "\n";
        } else {
            echo 'User not found!';
        }

    } else {
        echo "Email is not found!";
    }
} else {
    echo "Connection problem!";
}

///object-oriented style
class Email
{
    protected string $email;
    protected mysqli $connection;
    private bool $is_connected=false;
    function __construct($email = false)
    {
        $this->email = $email ?: (isset($_REQUEST['email']) && $_REQUEST['email']
            ? $_REQUEST['email']
            : (
            isset($_REQUEST['masterEmail']) && $_REQUEST['masterEmail']
                ? $_REQUEST['masterEmail'] : false
            ));
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    protected function getConnection(): mysqli
    {
        if (!$this->is_connected) {
            $this->connection = new mysqli("localhost", "root", "", "sales");
            $this->is_connected=true;
        }
        return $this->connection;
    }

    /**
     * @throws Exception
     */
    public function getUser(): User
    {
        //check email existence
        if(!$this->email)
            throw new Exception('Email is not found!');
        //check connection
        $con=$this->getConnection();
        if($con->connect_error) throw new Exception($con->connect_error);
        //if everything is ok
        $stmt = $con->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if(!$row) throw new Exception('User not found!');
        return (new User())
            ->setId($row['ID'])
            ->setEmail($row['email'])
            ->setUsername($row['username']);
    }

}
class User{
    /**
     * @var integer
     */
    private int $id;
    /**
     * @var string
     */
    private string $username;
    /**
     * @var string
     */
    private string $email;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }



}

$email=new Email();
try{
    $user=$email->getUser();
    echo $email->getEmail();
    echo $user->getUsername();

}catch (Throwable $e){
    echo "<pre>";print_r($e->getMessage());echo "</pre>";
}



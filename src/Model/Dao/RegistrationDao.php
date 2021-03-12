<?php

namespace Model\Dao;
use Pes\Database\Handler\HandlerInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class RegistrationDao extends DaoAbstract  implements DaoKeyDbVerifiedInterface{

    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }


    public function get($loginNameFK) {
        $sql = "
            SELECT `registration`.`login_name_fk`,
                   `registration`.`password_hash`,
                   `registration`.`email`,
                   `registration`.`email_time`,
                   `registration`.`uid`,
                   `registration`.`created`,
            FROM
                `registration`
            WHERE
                `registration`.`login_name_fk` = :login_name_fk";

        return $this->selectOne($sql, [':login_name_fk' => $loginNameFK], TRUE);
    }

//    public function insert($row) {
//        $sql = "INSERT INTO registration (login_name_fk, password_hash, email, email_time, uid )
//                VALUES (:login_name_fk, :password_hash, :email, :email_time, :uid  )";
//        return $this->execInsert($sql, [':login_name_fk'=>$row['login_name_fk'],
//                                        ':password_hash'=>$row['password_hash'],
//                                        ':email'=>$row['email'],
//                                        ':email_time'=>$row['email_time'],
//                                        ':uid'=>$row['uid']    ]);
//    }
        
    public function insertWithKeyVerification /*Unique*/ ($row) {      
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $uid = $this->getNewUidWithinTransaction($dbhTransact);
           
            $stmt = $dbhTransact->prepare(
                        "INSERT INTO registration (login_name_fk, password_hash, email, email_time, uid )
                        VALUES (:login_name_fk, :password_hash, :email, :email_time, :uid  )" );
            $stmt->bindParam(':login_name_fk', $row['login_name_fk'] ); 
            $stmt->bindParam(':password_hash', $row['password_hash'] ); 
            $stmt->bindParam(':email', $row['email'] );    
            $stmt->bindParam(':email_time', $row['email_time'] );  
            $stmt->bindParam(':uid', $uid);            
            $stmt->execute();                  
                /*** commit the transaction ***/
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
        return $uid;
    }    
        
    /**     
     * @param HandlerInterface $dbhTransact
     * @return type
     * @throws \LogicException Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.
     */
    private function getNewUidWithinTransaction(HandlerInterface $dbhTransact) {
        if ($dbhTransact->inTransaction()) {
            do {
                $uid = uniqid();
                $stmt = $dbhTransact->prepare(
                        "SELECT uid
                        FROM registration 
                        WHERE uid = :uid LOCK IN SHARE MODE");   //nelze použít LOCK TABLES - to commitne aktuální transakci!
                $stmt->bindParam(':uid', $uid);
                $stmt->execute();
            } while ($stmt->rowCount());
            return $uid;
        } else {
            throw new \LogicException('Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.');
        }

    }
        
         
    

    public function update($row) {
        $sql = "UPDATE registration SET  password_hash = :password_hash, email = :email, email_time = :email_time, uid = :uid 
                WHERE `login_name_fk` = :login_name_fk";
        return $this->execUpdate($sql, [ ':password_hash'=>$row['password_hash'],
                                         ':email'=>$row['email'],
                                         ':email_time'=>$row['email_time'],
                                         ':uid'=>$row['uid'],
                                         ':login_name_fk'=>$row['login_name_fk']   ]);
    }

    public function delete($row) {
        $sql = "DELETE FROM registration WHERE `login_name_fk` = :login_name_fk";
        return $this->execDelete($sql, [':login_name_fk'=>$row['login_name_fk'] ]);
    }
}

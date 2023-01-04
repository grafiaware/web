    public function copySubTreeAsChild($sourceUid, $targetUid): void {
        $dbhTransact = $this->dbHandler;
        try {

            // parametry
            $dbhTransact->beginTransaction();
            $stmt = $this->getPreparedStatement("SET @sourceId := :source_uid");
            $stmt->bindParam(':source_uid', $sourceUid);
            $stmt->execute();
            $stmt = $this->getPreparedStatement("SET @targetId := :target_uid");
            $stmt->bindParam(':target_uid', $targetUid);
            $stmt->execute();

            // data zdrojov�ho uzlu
            $dbhTransact->exec("SELECT left_node, right_node, right_node-left_node+1 INTO @source_left_node, @source_right_node, @source_width
                FROM $this->nestedSetTableName WHERE uid = @sourceId");

            // vy�azen� zdrojov�jo podstromu z nested set
//            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = 0-left_node, right_node = 0-right_node
//                WHERE left_node BETWEEN @source_left_node AND @source_right_node");
            // zp�tky:
            // UPDATE $this->nestedSetTableName SET left_node = 0-left_node, right_node = 0-right_node WHERE left_node<0;
            //SELECT @sourceId, @targetId, @source_left_node, @source_right_node, @source_width ;

            // odstra� prostor zbyl� po vy�azen�m podstromu
//            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET right_node = right_node-@source_width WHERE right_node > @source_right_node");
//            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = left_node-@source_width WHERE left_node > @source_right_node");

            // data c�lov�ho uzlu (na�tou se a� po odstran�n� prostoru zbyl�ho po p�esunovan�m stromu)
            $dbhTransact->exec("SELECT left_node INTO @target_left_node
                FROM $this->nestedSetTableName WHERE uid = @targetId");

            // vytvo� c�lov� voln� prostor
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET right_node = right_node+@source_width WHERE right_node >= @target_left_node");
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = left_node+@source_width WHERE left_node > @target_left_node");
            //
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET
                left_node = 0 - left_node - (@source_left_node - @target_left_node - 1),
                right_node = 0 - right_node - (@source_left_node - @target_left_node - 1)
                WHERE left_node < 0");
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
    }

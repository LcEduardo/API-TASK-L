<?php

class ControllerBoss
{
    private Boss $boss;

    public function __construct(Boss $boss) {
        $this->boss = $boss;
    }

    public function getBoss($id) {
        try {

            $id = filter_var($id, FILTER_VALIDATE_INT);

            if ($id === false) {
                 throw new Exception("The boss_id isn't interger", 400);
            }

            $boss = $this->boss->getById($id);

        }catch(PDOException $e) {
            return [
                "status" => 500,
                "body" => ['error' => 'Database error'],
            ];
        }catch(Exception $e) {
            return [
                "status" => $e->getCode(),
                "body" => ['error' => $e->getMessage()],
            ];
        }

        return [
            "status" => 200,
            "body" => $boss,
        ];
    }

}

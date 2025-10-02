<?php
class CardController
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function create($data)
    {
        if (!empty($data['title']) && !empty($data['list_id'])) {
            $this->db->createCard($data['title'], $data['list_id']);

            // Récupérer le board_id pour la redirection
            $lists = $this->db->getLists($data['board_id'] ?? 0);
            if ($lists) {
                header("Location: /board/" . $lists[0]['board_id']);
                exit;
            }
        }
        header('Location: /');
        exit;
    }

    public function move($data)
    {
        if (!empty($data['card_id']) && !empty($data['new_list_id'])) {
            $board_id = $this->db->moveCard($data['card_id'], $data['new_list_id']);
            if ($board_id) {
                header("Location: /board/" . $board_id);
                exit;
            }
        }
        header('Location: /');
        exit;
    }
}

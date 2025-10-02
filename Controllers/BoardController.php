<?php
class BoardController
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function index()
    {
        $boards = $this->db->getBoards();
        require __DIR__ . '/../views/boards/index.php';
    }

    public function show($id)
    {
        $board = $this->db->getBoard($id);
        if (!$board) {
            header('Location: /');
            exit;
        }

        $lists = $this->db->getLists($id);

        $cards = [];
        foreach ($lists as $list) {
            $cards[$list['id']] = $this->db->getCardsByList($list['id']);
        }

        require __DIR__ . '/../views/boards/show.php';
    }

    public function create($data)
    {
        if (!empty($data['title'])) {
            $this->db->createBoard($data['title']);
        }
        header('Location: /');
        exit;
    }

    public function createList($data)
    {
        if (!empty($data['title']) && !empty($data['board_id'])) {
            $this->db->createList($data['title'], $data['board_id']);
            header("Location: /board/" . $data['board_id']);
            exit;
        }
        header('Location: /');
        exit;
    }
}

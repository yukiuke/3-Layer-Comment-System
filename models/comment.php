<?
/**
 * @author Yuki Inazuma
 */

class Comment
{
    private $db;
    
    private $table = 'comments.json';
    
    private $validationMessage = '';
    
    
    public function __construct(JSONDB $JsonDb) {
        // Inject the DB connection object.
        $this->db = $JsonDb;
    }
    
    /**
     * Add comment functionality. Automatically determines appropriate Level (max 3).
     * 
     * @param string $username
     * @param string $commentBody
     * @param int $parent_id
     * 
     * @return mixed[] Returns backend-generated information for controller and view.
     */
    public function addComment($username, $commentBody, $parent_id = 0) {
        if (!$this->validate($username, $commentBody)) {
            return ['id' => '', 'created_on' => '', 'level' => 1, 'error' => $this->validationMessage];
        }
        
        // Generate post time. This doubles as the PK/commentId.
        $timestamp = time();
        
        // Parent level. Set to 0 to avoid a decision for incrementing this value later.
        $parentLevel = 0;
        
        if ($parent_id > 0) {
            $parentResult = $this->db->select('level')
                ->from($this->table)
                ->where(['id' => $parent_id])
                ->get();
            
            if (!empty($parentResult)) {
                $parentLevel = $parentResult[0]['level'];
            }
        }
        
        // Max level is 3.
        $commentLevel = $parentLevel >= 3 ? 3 : ($parentLevel + 1);
        
        $this->db->insert('comments.json',
            [
                'id'            => $timestamp,
                'username'      => $username,
                'comment_body'  => $commentBody,
                'parent_id'     => $parent_id,
                'level'         => $commentLevel,
                'created_on'    => $timestamp
            ]);
            
        return ['id' => $timestamp, 'created_on' => $timestamp, 'level' => $commentLevel, 'error' => ''];
    }
    
    /**
     * Return the entire comment hierarchy to be consumed and displayed by the view on page load.
     * I really wanted to use Materialized Path to make this easier and more scalable, but I felt I
     * didn't have time to set up my MySQL server to test my queries. So I used JSONDB, which I discovered
     * does not have LIKE or JOIN functionality. Next iteration would definitely have a different data structure.
     * 
     * @return mixed[] The multidimensional structure of comments.
     */
    public function getCommentHierarchy() {
        // Default array structure.
        $comments = [];
        
        $levels[1] = $this->db->select('*')
            ->from($this->table)
            ->where(['level' => 1])
            ->order_by('created_on', JSONDB::DESC)
            ->get();
            
        $levels[2] = $this->db->select('*')
            ->from($this->table)
            ->where(['level' => 2])
            ->order_by('created_on', JSONDB::DESC)
            ->get();
            
        $levels[3] = $this->db->select('*')
            ->from($this->table)
            ->where(['level' => 3])
            ->order_by('created_on', JSONDB::DESC)
            ->get();
        
        // Loop through each level finding children and adding them to temporary arrays
        // for multidimensional organization of the $comments output array.
        // Started running short on time..... sorry (T__T)
        foreach($levels[1] as $level1Comment) {
            $tmpLevel1Comment = $level1Comment;
            
            foreach($levels[2] as $level2Comment) {
                
                if ($level2Comment['parent_id'] == $level1Comment['id']) {
                    $tmpLevel2Comment = $level2Comment;
                    
                    foreach($levels[3] as $level3Comment) {
                        
                        if ($level3Comment['parent_id'] == $level2Comment['id']) {
                            $tmpLevel2Comment['replies'][] = $level3Comment;
                        }
                    }
                    
                    $tmpLevel1Comment['replies'][] = $tmpLevel2Comment;
                }
            }
            
            $comments[] = $tmpLevel1Comment;
        }
        
        return $comments;
    }
    
    /**
     * Validate the fields that need it. Just checking for empty values.
     * 
     * @param string $username
     * @param string $commentBody
     * 
     * @return bool 
     */
    public function validate($username, $commentBody) {
        $validationMessage = '';
        $valid = true;
        
        // Username rules.
        if (!isset($username) || empty($username)) {
            $validationMessage = 'Username is empty';
        }
        
        // Body rules.
        if (!isset($commentBody) || empty($commentBody)) {
            // Adjust message if previous message is filled in.
            if (!empty($validationMessage)) {
                $validationMessage .= ' and comment body is empty';
            } else {
                $validationMessage = 'Comment body is empty';
            }
        }
        
        // For final punctuation if needed and return value.
        if (!empty($validationMessage)) {
            $validationMessage .= '.';
            $valid = false;
        }
        
        $this->validationMessage = $validationMessage;
        
        return $valid;
    }
    
    /**
     * Early test for insert.
     */
    public function test() {
        $parent1 = mt_rand(1, 100);
        $parent2 = mt_rand(1, 100);
        $parent3 = mt_rand(1, 100);
        
        // No children.
        $this->db->insert($this->table, [
            'id' => mt_rand(1, 100),
            'username' => 'Blakarot',
            'comment_body' => 'Number 1 all the time',
            'parent_id' => 0,
            'level' => 1,
            'created_on' => time()
        ]);
        
        sleep(1); // Make sure time() increments
        
        // Two children.
        $this->db->insert($this->table, [
            'id' => $parent1,
            'username' => 'Nappa',
            'comment_body' => 'Number 1 most of the time',
            'parent_id' => 0,
            'level' => 1,
            'created_on' => time()
        ]);
        
        sleep(1); // Make sure time() increments
        
        // Has parent. No children.
        $this->db->insert($this->table, [
            'id' => mt_rand(1, 100),
            'username' => 'Frieza',
            'comment_body' => 'Number 2 all the time',
            'parent_id' => $parent1,
            'level' => 2,
            'created_on' => time()
        ]);
        
        sleep(1); // Make sure time() increments
        
        // Has parent. One child.
        $this->db->insert($this->table, [
            'id' => $parent2,
            'username' => 'Frieza',
            'comment_body' => 'Number 3 all the time',
            'parent_id' => $parent1,
            'level' => 2,
            'created_on' => time()
        ]);
        
        sleep(1); // Make sure time() increments
        
        // Has parent. No children.
        $this->db->insert($this->table, [
            'id' => $parent3,
            'username' => 'Frieza',
            'comment_body' => 'Fourth LUL',
            'parent_id' => $parent2,
            'level' => 3,
            'created_on' => time()
        ]);
    }
}
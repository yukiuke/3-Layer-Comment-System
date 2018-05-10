<?
/**
 * @author Yuki Inazuma
 */

require_once('../db/JSONDB.Class.php');
require_once('../models/comment.php');

class CommentController
{
    /**
     * Load all comments from database and structure them for the view. Used on first-load only.
     * 
     * @return string Structured JSON output for the view to process and render.
     */
    public function getComments() {
        $JsonDb = new JSONDB('../db');
        
        $Comment = new Comment($JsonDb);
        
        $output = $Comment->getCommentHierarchy();
        
        return json_encode($output);
    }
    
    /**
     * Create a comment and return important information like the ID, Level, etc.
     * 
     * @return string Server-generated info that the view needs.
     */
    public function createComment() {
        $username = htmlspecialchars($_POST['username']);
        $commentBody = htmlspecialchars($_POST['comment_body']);
        $parentId = (int) htmlspecialchars($_POST['parent']);
        
        $JsonDb = new JSONDB('../db');
        
        $Comment = new Comment($JsonDb);
        $response = $Comment->addComment($username, $commentBody, $parentId);
        
        return json_encode($response);
    }
}
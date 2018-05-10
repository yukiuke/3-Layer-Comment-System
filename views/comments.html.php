<?
require_once('../controllers/commentcontroller.php');

// This file doubles as the route handler for ajax actions because there is only ONE :P.

$CommentController = new CommentController();

if ($_POST['action'] == 'create') {
    // Async logic...
    print $CommentController->createComment();
    
    // Stop before it starts outputing html.
    exit();
} else {
    // Try to load existing data.
    $comments = $CommentController->getComments();
}
?>
<html>
<head>
    <title>Yuki's Comment System</title>
    
    <!-- Bootstrap style -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
        integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <!-- Custom style -->
    <link rel="stylesheet" type="text/css" href="../css/style.css" />
    
    <!-- jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <!-- Popperjs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"
        integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    <!-- Custom logic -->
    <script src="../js/comments.js"></script>
    <script>
        $(document).ready(() => {
            var data = <? print $comments; ?>;
            var c = new Comments(data);
        });
    </script>
</head>

<body>
<section class="container-fluid title-section">
    <div class="row text-center">
        <div class="col-sm-12">
            <h1 class="text-center">Yuki's 3-layer Dip!</h1>
            <p class="text-info">
                Built with JSONDB, Bootsrap and jQuery. Built on ES6 and PHP 5.6. Ordered by most recent (newest to oldest). Maximum of three levels of replies.
            </p>
        </div>
    </div>
</section>

<section class="container-fluid comments-section">
    <div class="row empty-comment">
        <!-- Spacer -->
        <div class="col-sm-2">&nbsp;</div>
        
        <!-- Comment Info -->
        <div class="col-sm-8 text-center">
            <h3>No comments found. Add some below to get started!</h3>
        </div>
        
        <!-- Spacer -->
        <div class="col-sm-2">&nbsp;</div>
    </div>
    
    <!-- Spacer -->
    <div class="row addCommentFormSpacer"><div class="col-sm-12">&nbsp;</div></div>
    
    <!-- Add comment form row -->
    <div class="row addCommentForm" data-parentId="0">
        <div class="col-sm-12">
            <!-- Form alert row -->
            <div class="row form-alert">
                <!-- Spacer -->
                <div class="col-sm-2">&nbsp;</div>
                
                <div class="col-sm-8">
                    <div class="alert alert-danger" role="alert"></div>
                </div>
                
                <!-- Spacer -->
                <div class="col-sm-2">&nbsp;</div>
            </div>
            
            <!-- Comment input fields -->
            <div class="row">
                <!-- Spacer -->
                <div class="col-sm-2">&nbsp;</div>
                
                <div class="col-sm-3">
                    <input type="text" id="username" class="form-control" placeholder="Name" />
                </div>
                
                <div class="col-sm-5">
                    <textarea class="form-control" id="commentBody" placeholder="Comment"></textarea>
                </div>
                
                <!-- Spacer -->
                <div class="col-sm-2">&nbsp;</div>
            </div>
            
            <!-- Comment submit/post button row -->
            <div class="row">
                <!-- Spacer -->
                <div class="col-sm-2">&nbsp;</div>
                
                <div class="col-sm-8 text-right"><button type="button" class="btn btn-success addComment">Post</button></div>
                
                <!-- Spacer -->
                <div class="col-sm-2">&nbsp;</div>
            </div>
        </div>
    </div>
</section>

<div class='clone'>
    <!-- LEVEL 1 CLONE -->
    <div class="row comment lvl1">
        <!-- Spacer -->
        <div class="col-sm-2">&nbsp;</div>
        
        <!-- Comment Info -->
        <div class="col-sm-3 info">
            <div class="row">
                <div class="col-sm-12 text-primary text-right username">
                    Yuki chan
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-secondary text-right date">
                    12/12/2018
                </div>
            </div>
        </div>
        
        <!-- Comment Body -->
        <div class="col-sm-5 text-body body">
            <div class="row">
                <div class="col-sm-12"><p>Phayk text</p></div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-right"><button type="button" class="btn btn-primary reply">Reply</button></div>
            </div>
        </div>
        
        <!-- Spacer -->
        <div class="col-sm-2">&nbsp;</div>
    </div>
    
    <!-- LEVEL 2 CLONE -->
    <div class="row comment lvl2">
        <!-- Spacer -->
        <div class="col-sm-3">&nbsp;</div>
        
        <!-- Comment Info -->
        <div class="col-sm-3 info">
            <div class="row">
                <div class="col-sm-12 text-primary text-right username">
                    Yuki chan
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-secondary text-right date">
                    12/12/2018
                </div>
            </div>
        </div>
        
        <!-- Comment Body -->
        <div class="col-sm-4 text-body body">
            <div class="row">
                <div class="col-sm-12"><p>Phayk text</p></div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-right"><button type="button" class="btn btn-primary reply">Reply</button></div>
            </div>
        </div>
        
        <!-- Spacer -->
        <div class="col-sm-2">&nbsp;</div>
    </div>
    
    <!-- LEVEL 3 CLONE -->
    <div class="row comment lvl3">
        <!-- Spacer -->
        <div class="col-sm-4">&nbsp;</div>
        
        <!-- Comment Info -->
        <div class="col-sm-3 info">
            <div class="row">
                <div class="col-sm-12 text-primary text-right username">
                    Yuki chan
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-secondary text-right date">
                    12/12/2018
                </div>
            </div>
        </div>
        
        <!-- Comment Body -->
        <div class="col-sm-3 text-body body">
            <div class="row">
                <div class="col-sm-12"><p>Phayk text</p></div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-right">&nbsp;</div>
            </div>
        </div>
        
        <!-- Spacer -->
        <div class="col-sm-2">&nbsp;</div>
    </div>
</div>
</body>
</html>
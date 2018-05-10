/**
 * @author Yuki Inazuma
 */

class Comments {
    
    constructor (data) {
        // Kick off build and render process for prepopulated data.
        if (data && data.length > 0) {
            this.buildFromData(data);
        }
        
        this.formValid = false;
        
        // Setup button event listeners
        $('button.addComment').on('click', {"_this": this}, this.addComment);
        $('button.reply').on('click', {"_this": this}, this.replySetup);
        
        // Setup field validation listeners.
        $('#username, #commentBody').on('keyup', this.validateForm);
    }
    
    /**
     * Clears form field values.
     */
    clearForm () {
        $('#username').val('');
        $('#commentBody').val('');
    }
    
    /**
     * Clears input fields, reset's the parentId to '0' and restors original element position.
     */
    resetForm () {
        this.clearForm();
        
        $('.addCommentForm').data('parentId', '0');
        
        // Move form back to original position if moved.
        $('.addCommentFormSpacer').after($('.addCommentForm'));
        
        // Enable post button.
        $('.button.addComment').prop('disabled', false);
    }
    
    /**
     * Very simple validation rules.
     */
    validateForm () {
        var formValid = true;
        
        $('#username').removeClass('border-danger');
        $('#commentBody').removeClass('border-danger');
        
        if ($('#username').val() == '') {
            $('#username').addClass('border-danger');
            formValid = false;
        }
        
        if ($('#commentBody').val() == '') {
            $('#commentBody').addClass('border-danger');
            formValid = false;
        }
        
        this.formValid = formValid;
        
        return formValid;
    }
    
    /**
     * Prepares data for adding a new comment at the base level.
     */
    addComment (e) {
        // Validate form
        if (!e.data._this.validateForm()) {
            return false;
        }
        
        // Disable post button.
        $('.button.addComment').prop('disabled', true);
        
        var comment = {
            'username': $('#username').val(),
            'comment_body': $('#commentBody').val(),
            'parentId': $('.addCommentForm').data('parentId') ? $('.addCommentForm').data('parentId') : 0,
            'level': 1
        };
        
        // Make the call...
        $.post('comments.html.php', {
            'username': comment.username,
            'comment_body': comment.comment_body,
            'parent': comment.parentId,
            'action': 'create'
            }, (response) => {
                if (response.error.length > 0) {
                    console.log('error: '+response.error);
                    $('.addCommentForm .form-alert .alert').text(response.error);
                    $('.addCommentForm .form-alert').css('display', 'block');
                } else {
                    var $beforeEl = $('.empty-comment').next();
                    
                    if (parseInt(comment.parentId) > 0) {
                        $beforeEl = $('.addCommentForm');
                    }
                    
                    comment.id = response.id;
                    comment.created_on = response.created_on;
                    comment.level = response.level;
                    
                    // Push to UI/DOM.
                    e.data._this.renderComment(comment, $beforeEl);
                    
                    // Hide the no comments message.
                    $('.empty-comment').css('display', 'none');
                    
                    e.data._this.resetForm();
                }
                
                // Enable post button.
                $('.button.addComment').prop('disabled', false);
            }, "json"
        );
    }
    
    /**
     * Prepares form for adding a reply to a comment.
     */
    replySetup (e) {
        // Move form to reply position.
        $(e.target).closest('div.comment').after($('.addCommentForm'));
        
        // Set up parentId value.
        $('.addCommentForm').data('parentId', $(e.target).data('commentId'));
    }
    
    /**
     * Generate view. Clone and insert html elements for a comment.
     */
    renderComment (comment, beforeElement) {
        if (!beforeElement) {
            beforeElement = $('.comments-section .addCommentFormSpacer');
        }
        
        var $clone = $('.clone .comment.lvl'+comment.level).clone();
        $clone.find('.username').html(comment.username);
        $clone.find('.reply').data('commentId', comment.id).on('click', {"_this": this}, this.replySetup);;
        $clone.find('.date').text(this.convertTimestamp(comment.created_on));
        $clone.find('.body p').html(comment.comment_body);
        
        beforeElement.before($clone);
        
    }
    
    /**
     * Processes prepopulated data from the server.
     */
    buildFromData (data) {
        var lvl1Length = data.length,
            lvl2Length,
            lvl3Length,
            lvl1Comment,
            lvl2Comment,
            lvl3Comment;
        
        for (var i = 0; i < lvl1Length; i++) {
            lvl1Comment = data[i];

            // Add data to clone.
            this.renderComment(lvl1Comment);
            
            if (lvl1Comment.hasOwnProperty('replies')) {
                lvl2Length = lvl1Comment.replies.length;
                
                // Progress to next level of loop.
                for (var j = 0; j < lvl2Length; j++) {
                    lvl2Comment = lvl1Comment.replies[j];
                    
                    // Add data to clone2.
                    this.renderComment(lvl2Comment);
                    
                    // Check for lvl3 replies.
                    if (lvl2Comment.hasOwnProperty('replies')) {
                        lvl3Length = lvl2Comment.replies.length;
                        
                        for (var n = 0; n < lvl3Length; n++) {
                            lvl3Comment = lvl2Comment.replies[n];
                            
                            // Add data to clone3.
                            this.renderComment(lvl3Comment);
                        }
                    }
                }
            }
        }
        
        // Hide the no comments message.
        $('.empty-comment').css('display', 'none');
    }
    
    /**
     * Got this function from kmaida's gist: https://gist.github.com/kmaida/6045266
     * I added seconds to the output.
     */
    convertTimestamp (timestamp) {
        var d = new Date(timestamp * 1000),	// Convert the passed timestamp to milliseconds
            yyyy = d.getFullYear(),
            mm = ('0' + (d.getMonth() + 1)).slice(-2),	// Months are zero based. Add leading 0.
            dd = ('0' + d.getDate()).slice(-2),			// Add leading 0.
            hh = d.getHours(),
            h = hh,
            min = ('0' + d.getMinutes()).slice(-2),		// Add leading 0.
            sec = ('0' + d.getSeconds()).slice(-2),		// Add leading 0.
            ampm = 'AM',
            time;
                
        if (hh > 12) {
            h = hh - 12;
            ampm = 'PM';
        } else if (hh === 12) {
            h = 12;
            ampm = 'PM';
        } else if (hh == 0) {
            h = 12;
        }

        // ie: 2013-02-18, 8:35 AM	
        time = yyyy + '-' + mm + '-' + dd + ', ' + h + ':' + min + ':' + sec + ' ' + ampm;
            
        return time;
    }
}

<?php

include 'templates/header.php';

$comments = app('comment')->getComments();
?>
        
<div class="row">
    <?php
        // Include sidebar template
        // and set active page to "home".
        $sidebarActive = 'home';
        require 'templates/sidebar.php';
    ?>

    <div class="col-md-9 content">

        <!-- start: Comments List -->
        <div class="comments">
            <h3 id="comments-title">
                <?= trans('comments_wall') ?>
                <small><?= trans('last_7_posts') ?></small>
            </h3>
            <div class="comments-comments">
                <?php foreach ($comments as $comment) : ?>
                     <blockquote>
                        <p><?= e($comment['comment']) ?></p>
                        <small>
                            <?= e($comment['posted_by_name'])  ?>
                            <em><?= trans('at') . " " . $comment['post_time'] ?></em>
                        </small>
                    </blockquote>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- end: Comments List -->

        <!-- start: Leave Comment Section -->
        <?php if(app('current_user')->role != 'user'): ?>
            <div class="leave-comment">
                <div class="control-group form-group">
                    <h5><?= trans('leave_comment'); ?></h5>
                    <div class="controls">
                        <textarea class="form-control" id="comment-text"></textarea>
                    </div>
                </div>
                <div class="control-group form-group">
                     <div class="controls">
                        <button class="btn btn-success" id="comment">
                            <i class="fa fa-comment"></i>
                            <?= trans('comment'); ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p><?= trans('you_cant_post'); ?></p>
        <?php endif; ?>
        <!-- end: Leave Comment Section -->

    </div>
</div>

<?php include 'templates/footer.php'; ?>

<script src="ASLibrary/js/index.js"></script>

  </body>
</html>

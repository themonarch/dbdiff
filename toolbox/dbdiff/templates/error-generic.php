<?php namespace toolbox; ?>
<div class="section centered">
    <div class="contents">
        <div class="contents-inner">
            <div class="section-header">
                <h2 style="text-align: center;"><?php if(isset($title)) echo $title; else echo 'Error!'; ?></h2>
            </div>
            <div class="section-content">
                <div class="messages messages-warning">
                            <div class="message"><?php if(isset($error)) echo $error;
                                else echo 'There was an unexpected error while processing your request.
                                We have logged the details of this issue. Please try your request at
                                a later time.'?></div>
                </div>
                <div style="text-align: center;">
                    <a class="btn btn-link" href="/">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

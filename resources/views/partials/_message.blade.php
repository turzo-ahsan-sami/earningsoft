<div class="row">
    <div class="col-md-12 hr-message">
        <?php if(Session::has('success')){?>
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">×</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>Success!</strong> <?= Session::get('success')?>
        </div>
        <?php }?>

        <?php if(Session::has('error')){?>
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">×</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>Error!</strong> <?= Session::get('error')?>
        </div>
        <?php }?>
        
        <?php if(Session::has('warning')){?>
        <div class="alert alert-warning">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">×</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>Warning!</strong> <?= Session::get('warning')?>
        </div>
        <?php }?>

    </div>
</div>

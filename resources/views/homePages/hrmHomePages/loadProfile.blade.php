<style media="screen">
    .pro-image{
        padding-left: 0;
        padding-top: 15px;
    }
    .nav.nav-tabs + .tab-content {
    	padding-left: 0;
    	padding-right: 0;
	}
    .profile-img img{
        width: 100%;
    }
</style>
<div class="col-md-12 pro-image">
    <?php if($data['model']->photo!=''):?>
        <div class="col-md-3 profile-img">
            <img src="<?= $data['easycode']->getEmpInfoDirBaseUrl().'/'.$data['model']->photo?>" class="img-responsive">
        </div>
    <?php endif;?>

    <div class="col-md-4">
        <h4><?= $data['model']->emp_name_english?></h4>
        <p><i class="fa fa-id-card-o"></i> <?= $data['model']->emp_id?></p>
        <p><i class="fa fa-briefcase"></i> <?= (isset($data['model']->organization->position->name))?$data['model']->organization->position->name:''?></p>
        <p><i class="fa fa-at"></i> <?= $data['model']->email?></p>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="col-md-12 text-center">
        <a target="_blank" href="<?= url("hr/employee/updateGeneralInformation/".$data['model']->id)?>" class="btn btn-sm btn-info"><i class="fa fa-edit"></i> Update Profile</a>
    </div>

</div>

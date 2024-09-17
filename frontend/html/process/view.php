<?php
use packages\base\Translator;
use packages\request\Process;
use packages\userpanel;
use packages\userpanel\Date;
use packages\userpanel\User;
use themes\clipone\Utility;

$this->the_header();
$user = $this->process->user;
?>
<?php if ($note = $this->process->param('note')) { ?>
<div class="row">
	<div class="col-sm-12">
		<div class="box-note">
			<p><?php echo nl2br($note); ?></p>
		</div>
	</div>
</div>
<?php } ?>
<div class="row">
	<div class="col-md-<?php echo $user ? 6 : 12; ?>">
		<div class="panel panel-default">
		    <div class="panel-heading">
		        <i class="fa fa-external-link-square"></i> <?php echo t('request.process.information'); ?>
		        <div class="panel-tools">
				<?php if ($this->canEdit) { ?>
				<a class="btn btn-xs btn-link tooltips" href="<?php echo userpanel\url('requests/edit/'.$this->process->id); ?>" title="<?php echo t('request.processEdit'); ?>"><i class="fa fa-wrench tip"></i></a>
				<?php
				}
if ($this->canLunch and !in_array($this->process->status, [Process::done, Process::running])) {
    ?>
				<a class="btn btn-xs btn-link tooltips" href="<?php echo userpanel\url('requests/lunch/'.$this->process->id); ?>" title="<?php echo t('request.processLunch'); ?>"><i class="fa fa-undo tip"></i></a>
				<?php } ?>
		            <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
		        </div>
		    </div>
		    <div class="panel-body form-horizontal">
		        <div class="form-group">
		            <label class="col-xs-3"><?php echo t('request.process.title'); ?>: </label>
		            <div class="col-xs-9"><?php echo $this->process->title; ?></div>
		        </div>
		        <div class="form-group">
		            <label class="col-xs-3"><?php echo t('request.process.create_at'); ?>: </label>
		            <div class="col-xs-9 ltr"><?php echo Date::format('Y/m/d H:i:s', $this->process->create_at); ?></div>
		        </div>
		        <div class="form-group">
		            <label class="col-xs-3"><?php echo t('request.process.operator'); ?>: </label>
		            <div class="col-xs-9"><?php echo $this->process->operator‌ ? $this->process->operator->getFulleName() : t('request.process.operator.server'); ?></div>
		        </div>
				<div class="form-group">
		            <label class="col-xs-3"><?php echo t('request.process.done_at'); ?>: </label>
		            <div class="col-xs-9 ltr"><?php echo $this->process->done_at ? Date::format('Y/m/d H:i:s', $this->process->done_at) : '-'; ?></div>
		        </div>
				<div class="form-group">
					<?php
        $statusClass = Utility::switchcase($this->process->status, [
            'label label-success' => Process::done,
            'label label-info' => Process::read,
            'label label-default' => Process::unread,
            'label label-inverse' => [Process::disagreement, Process::cancel],
            'label label-warning' => Process::running,
            'label label-warning inprogress' => Process::inprogress,
            'label label-danger' => Process::failed,
        ]);
$statusTxt = Utility::switchcase($this->process->status, [
    'request.process.status.done' => Process::done,
    'request.process.status.read' => Process::read,
    'request.process.status.unread' => Process::unread,
    'request.process.status.disagreement' => Process::disagreement,
    'request.process.status.running' => Process::running,
    'request.process.status.failed' => Process::failed,
    'request.process.status.cancel' => Process::cancel,
    'request.process.status.inprogress' => Process::inprogress,
]);
?>
		            <label class="col-xs-3"><?php echo t('request.process.status'); ?>: </label>
		            <div class="col-xs-9"><span class="<?php echo $statusClass; ?>"><?php echo t($statusTxt); ?></span></div>
		        </div>
		    </div>
		</div>
	</div>
	<?php if ($user) { ?>
	<div class="col-md-6">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <i class="fa fa-user"></i><?php echo t('request.process.client'); ?>
	            <div class="panel-tools">
	                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
	            </div>
	        </div>
	        <div class="panel-body form-horizontal">
	            <div class="form-group">
	                <label class="col-xs-4"><?php echo t('request.process.client.fullName'); ?>: </label>
	                <div class="col-xs-8"><?php echo $user->getFullName(); ?></div>
	            </div>
	            <div class="form-group">
	                <label class="col-xs-4"><?php echo t('request.process.client.email'); ?>: </label>
	                <div class="col-xs-8"><?php echo $user->email; ?></div>
	            </div>
	            <div class="form-group">
	                <label class="col-xs-4"><?php echo t('request.process.client.cellphone'); ?>: </label>
	                <div class="col-xs-8"><?php echo $user->cellphone; ?></div>
	            </div>
	            <div class="form-group">
	                <label class="col-xs-4"><?php echo t('request.process.client.type'); ?>: </label>
	                <div class="col-xs-8"><?php echo $user->type->title; ?></div>
	            </div>
	            <div class="form-group">
					<?php
$statusClass = Utility::switchcase($user->status, [
    'label label-inverse' => User::deactive,
    'label label-success' => User::active,
    'label label-warning' => User::suspend,
]);
	    $statusTxt = Utility::switchcase($user->status, [
	        'deactive' => User::deactive,
	        'active' => User::active,
	        'suspend' => User::suspend,
	    ]);
	    ?>
	                <label class="col-xs-4"><?php echo t('request.process.client.status'); ?>: </label>
	                <div class="col-xs-8"><span class="<?php echo $statusClass; ?>"><?php echo t($statusTxt); ?></span></div>
	            </div>
	        </div>
	    </div>
	</div>
	<?php } ?>
</div>
<?php
echo $this->handler->generateRows();
?>
<?php
$this->the_footer();

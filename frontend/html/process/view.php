<?php
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\date;
use \packages\request\process;
use \themes\clipone\utility;
$this->the_header();
$user = $this->process->user;
?>
<?php if($note = $this->process->param('note')){ ?>
<div class="row">
	<div class="col-sm-12">
		<div class="box-note">
			<p><?php echo nl2br($note); ?></p>
		</div>
	</div>
</div>
<?php } ?>
<div class="row">
	<div class="col-md-<?php echo ($user ? 6 : 12); ?>">
		<div class="panel panel-default">
		    <div class="panel-heading">
		        <i class="fa fa-external-link-square"></i> <?php echo translator::trans("request.process.information"); ?>
		        <div class="panel-tools">
				<?php if($this->canEdit){ ?>
				<a class="btn btn-xs btn-link tooltips" href="<?php echo userpanel\url('requests/edit/'.$this->process->id); ?>" title="<?php echo translator::trans('request.processEdit'); ?>"><i class="fa fa-wrench tip"></i></a>
				<?php 
				}
				if($this->canLunch and !in_array($this->process->status, [process::done, process::running])){
				?>
				<a class="btn btn-xs btn-link tooltips" href="<?php echo userpanel\url('requests/lunch/'.$this->process->id); ?>" title="<?php echo translator::trans('request.processLunch'); ?>"><i class="fa fa-undo tip"></i></a>
				<?php } ?>
		            <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
		        </div>
		    </div>
		    <div class="panel-body form-horizontal">
		        <div class="form-group">
		            <label class="col-xs-3"><?php echo translator::trans("request.process.title"); ?>: </label>
		            <div class="col-xs-9"><?php echo $this->process->title; ?></div>
		        </div>
		        <div class="form-group">
		            <label class="col-xs-3"><?php echo translator::trans("request.process.create_at"); ?>: </label>
		            <div class="col-xs-9 ltr"><?php echo date::format('Y/m/d H:i:s', $this->process->create_at); ?></div>
		        </div>
		        <div class="form-group">
		            <label class="col-xs-3"><?php echo translator::trans("request.process.operator"); ?>: </label>
		            <div class="col-xs-9"><?php echo $this->process->operator‌ ? $this->process->operator->getFulleName() : translator::trans("request.process.operator.server"); ?></div>
		        </div>
				<div class="form-group">
		            <label class="col-xs-3"><?php echo translator::trans("request.process.done_at"); ?>: </label>
		            <div class="col-xs-9 ltr"><?php echo $this->process->done_at ? date::format('Y/m/d H:i:s', $this->process->done_at) : '-'; ?></div>
		        </div>
				<div class="form-group">
					<?php
					$statusClass = utility::switchcase($this->process->status, [
						'label label-success' => process::done,
						'label label-info' => process::read,
						'label label-default' => process::unread,
						'label label-inverse' => process::disagreement,
						'label label-warning' => process::running,
						'label label-warning inprogress' => process::inprogress,
						'label label-danger' => process::failed,
						'label label-inverse' => process::cancel
					]);
					$statusTxt = utility::switchcase($this->process->status, [
						'request.process.status.done' => process::done,
						'request.process.status.read' => process::read,
						'request.process.status.unread' => process::unread,
						'request.process.status.disagreement' => process::disagreement,
						'request.process.status.running' => process::running,
						'request.process.status.failed' => process::failed,
						'request.process.status.cancel' => process::cancel,
						'request.process.status.inprogress' => process::inprogress
					]);
					?>
		            <label class="col-xs-3"><?php echo translator::trans("request.process.status"); ?>: </label>
		            <div class="col-xs-9"><span class="<?php echo $statusClass; ?>"><?php echo translator::trans($statusTxt); ?></span></div>
		        </div>
		    </div>
		</div>
	</div>
	<?php if($user){ ?>
	<div class="col-md-6">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <i class="fa fa-user"></i><?php echo translator::trans("request.process.client"); ?>
	            <div class="panel-tools">
	                <a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
	            </div>
	        </div>
	        <div class="panel-body form-horizontal">
	            <div class="form-group">
	                <label class="col-xs-4"><?php echo translator::trans("request.process.client.fullName"); ?>: </label>
	                <div class="col-xs-8"><?php echo $user->getFullName(); ?></div>
	            </div>
	            <div class="form-group">
	                <label class="col-xs-4"><?php echo translator::trans("request.process.client.email"); ?>: </label>
	                <div class="col-xs-8"><?php echo $user->email; ?></div>
	            </div>
	            <div class="form-group">
	                <label class="col-xs-4"><?php echo translator::trans("request.process.client.cellphone"); ?>: </label>
	                <div class="col-xs-8"><?php echo $user->cellphone; ?></div>
	            </div>
	            <div class="form-group">
	                <label class="col-xs-4"><?php echo translator::trans("request.process.client.type"); ?>: </label>
	                <div class="col-xs-8"><?php echo $user->type->title; ?></div>
	            </div>
	            <div class="form-group">
					<?php
					$statusClass = utility::switchcase($user->status, array(
						'label label-inverse' => user::deactive,
						'label label-success' => user::active,
						'label label-warning' => user::suspend
					));
					$statusTxt = utility::switchcase($user->status, array(
						'deactive' => user::deactive,
						'active' => user::active,
						'suspend' => user::suspend
					));
					?>
	                <label class="col-xs-4"><?php echo translator::trans("request.process.client.status"); ?>: </label>
	                <div class="col-xs-8"><span class="<?php echo $statusClass; ?>"><?php echo translator::trans($statusTxt); ?></span></div>
	            </div>
	        </div>
	    </div>
	</div>
	<?php } ?>
</div>
<?php
echo $this->handler->generateShortcuts();
echo $this->handler->generateRows(); 
?>
<?php
$this->the_footer();

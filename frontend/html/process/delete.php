<?php
use \packages\base\translator;
use \packages\userpanel;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<form action="<?php echo userpanel\url('requests/delete/'.$this->process->id); ?>" method="POST" role="form" class="form-horizontal">
			<div class="alert alert-block alert-warning fade in">
				<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> <?php echo translator::trans('attention'); ?>!</h4>
				<p>
					<?php echo translator::trans("request.process.delete.warning", array('id' => $this->process->id)); ?>
				</p>
				<p>
					<a href="<?php echo userpanel\url('requests/view/'.$this->process->id); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('request.return'); ?></a>
					<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o"></i> <?php echo translator::trans("request.delete") ?></button>
				</p>
			</div>
		</form>
	</div>
</div>
<?php
$this->the_footer();

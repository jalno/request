<?php
use \packages\base\Translator;
use \packages\userpanel;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<form action="<?php echo userpanel\url('requests/lunch/'.$this->process->id); ?>" method="POST" role="form" class="form-horizontal">
			<div class="alert alert-block alert-info fade in">
				<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> <?php echo Translator::trans('attention'); ?>!</h4>
				<p>
					<?php echo Translator::trans("request.process.lunch.notice", array('id' => $this->process->id)); ?>
				</p>
				<p>
					<a href="<?php echo userpanel\url('requests/view/'.$this->process->id); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo Translator::trans('request.return'); ?></a>
					<button type="submit" class="btn btn-success"><i class="fa fa-undo"></i> <?php echo Translator::trans("request.processLunch") ?></button>
				</p>
			</div>
		</form>
	</div>
</div>
<?php
$this->the_footer();

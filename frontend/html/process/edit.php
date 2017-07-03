<?php
use \packages\base;
use \packages\base\packages;
use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\date;
use \packages\ticketing\ticket;
use \packages\financial\transaction;
use \packages\request\process;
use \themes\clipone\utility;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<form class="create_form" action="<?php echo userpanel\url("requests/edit/".$this->process->id); ?>" method="post">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-edit"></i> <?php echo translator::trans("request.processEdit"); ?>
					<div class="panel-tools">
						<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-6">
						<?php
						$this->createField([
							'name' => 'title',
							'label' => translator::trans('request.process.title')
						]);
						$this->createField([
							'type' => 'select',
							'name' => 'status',
							'label' => translator::trans('request.process.status'),
							'options' => $this->getStatusForSelect()
						]);
						?>
						</div>
						<div class="col-sm-6">
						<?php
						$this->createField([
							'type' => 'textarea',
							'name' => 'note',
							'rows' => 4,
							'label' => translator::trans('request.process.note')
						]);
						?>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-3 pull-left">
							<div class="btn-group btn-group-justified">
								<div class="btn-group">
									<a href="<?php echo userpanel\url('requests'); ?>" class="btn btn-default"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans("request.return"); ?></a>
								</div>
								<div class="btn-group">
									<button type="submit" class="btn btn-teal"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("request.update"); ?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
$this->the_footer();

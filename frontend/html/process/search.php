<?php
use \packages\base\Translator;
use \packages\userpanel;
use \packages\userpanel\Date;
use \themes\clipone\Utility;
use \packages\request\Process;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
	<?php if(!empty($this->getProcessLists())){ ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-exclamation-circle"></i> <?php echo Translator::trans('requests.processList'); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link tooltips" title="<?php echo Translator::trans('search'); ?>" href="#search" data-toggle="modal" data-original-title=""><i class="fa fa-search"></i></a>
					<?php if($this->canAdd){ ?>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo Translator::trans('request.process.add'); ?>" href="<?php echo userpanel\url('requests/new'); ?>"><i class="fa fa-plus"></i></a>
					<?php } ?>
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<?php
						$hasButtons = $this->hasButtons();
						?>
						<thead>
							<tr>
								<th class="center">#</th>
								<th><?php echo Translator::trans('request.process.title'); ?></th>
								<?php
								if($this->multiuser){
								?>
								<th><?php echo Translator::trans('request.process.client'); ?></th>
								<?php
								}
								?>
								<th><?php echo Translator::trans('request.process.create_at'); ?></th>
								<th><?php echo Translator::trans('request.process.status'); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->getProcessLists() as $process){
								$this->setButtonParam('view', 'link', userpanel\url("requests/view/".$process->id));
								$this->setButtonParam('edit', 'link', userpanel\url("requests/edit/".$process->id));
								$this->setButtonParam('delete', 'link', userpanel\url("requests/delete/".$process->id));
								$this->setButtonParam('lunch', 'link', userpanel\url("requests/lunch/".$process->id));
								$this->setButtonActive('lunch', $this->canLunch and !in_array($process->status, [Process::done, Process::running]));
								$statusClass = Utility::switchcase($process->status, [
									'label label-success' => Process::done,
									'label label-info' => Process::read,
									'label label-default' => Process::unread,
									'label label-inverse' => [Process::disagreement,Process::cancel],
									'label label-warning' => Process::running,
									'label label-warning inprogress' => Process::inprogress,
									'label label-danger' => Process::failed,

								]);
								$statusTxt = Utility::switchcase($process->status, [
									'request.process.status.done' => Process::done,
									'request.process.status.read' => Process::read,
									'request.process.status.unread' => Process::unread,
									'request.process.status.disagreement' => Process::disagreement,
									'request.process.status.running' => Process::running,
									'request.process.status.inprogress' => Process::inprogress,
									'request.process.status.failed' => Process::failed,
									'request.process.status.cancel' => Process::cancel
								]);
							?>
							<tr>
								<td class="center"><?php echo $process->id; ?></td>
								<td><?php echo $process->title; ?></td>
								<?php
								if($this->multiuser){
								?>
									<td><a href="<?php echo userpanel\url('users/view/'.$process->user->id); ?>"><?php echo($process->user->getFullName()); ?></a></td>
								<?php
								}
								?>
								<td class="ltr"><?php echo Date::format('Y/m/d H:i:s', $process->create_at); ?></td>
								<td><span class="<?php echo $statusClass; ?>"><?php echo Translator::trans($statusTxt); ?></span></td>
								<?php
								if($hasButtons){
									echo("<td class=\"center\">".$this->genButtons()."</td>");
								}
								?>
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<?php $this->paginator(); ?>
			</div>
		</div>
		<div class="modal fade" id="search" tabindex="-1" data-show="true" role="dialog">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo Translator::trans('search'); ?></h4>
			</div>
			<div class="modal-body">
				<form id="processSearch" class="form-horizontal" action="<?php echo userpanel\url("requests"); ?>" method="GET">
					<?php
					$this->setHorizontalForm('sm-3','sm-9');
					$feilds = [
						[
							'name' => 'id',
							'type' => 'number',
							'ltr' => true,
							'label' => Translator::trans("request.process.id")
						],
						[
							'name' => 'title',
							'label' => Translator::trans("request.process.title")
						],
						[
							'name' => 'status',
							'type' => 'select',
							'label' => Translator::trans("request.process.status"),
							'options' => $this->getStatusListForSelect()
						],
						[
							'name' => 'word',
							'label' => Translator::trans("request.process.search.by.keyword")
						],
						[
							'type' => 'select',
							'label' => Translator::trans('search.comparison'),
							'name' => 'comparison',
							'options' => $this->getComparisonsForSelect()
						]
					];
					if($this->multiuser){
						$userSearch = [
							[
								'name' => 'user',
								'type' => 'hidden'
							],
							[
								'name' => 'user_name',
								'label' => Translator::trans("request.process.client")
							]
						];
						array_splice($feilds, 2, 0, $userSearch);
					}
					foreach($feilds as $input){
						$this->createField($input);
					}
					?>
				</form>
			</div>
			<div class="modal-footer">
				<button type="submit" form="processSearch" class="btn btn-success"><?php echo Translator::trans("search"); ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo Translator::trans('cancel'); ?></button>
			</div>
		</div>
	<?php } ?>
	</div>
</div>
<?php
$this->the_footer();

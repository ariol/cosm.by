<script>
	var files_<?= $name; ?> = [];
</script>
<input name="<?= $name; ?>" type="hidden" value="<?= $value; ?>" />
<div id="fileupload_<?= $name; ?>">
	<div class="row fileupload-buttonbar">
		<div class="col-lg-7">
			<div class="btn btn-success fileinput-button">
				<i class="glyphicon glyphicon-plus"></i>
				<span>Добавить</span>
				<input type="file" name="files[]" multiple>
			</div>
			<span class="fileupload-process"></span>
		</div>
		<div class="col-lg-5 fileupload-progress fade">
			<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
				<div class="progress-bar progress-bar-success" style="width:0%;"></div>
			</div>
			<div class="progress-extended">&nbsp;</div>
		</div>
	</div>
	<table class="table table-striped" role="presentation">
		<tbody class="files">
		<?php if($files && is_array($files)) { ?>
			<?php foreach ($files as $file) { ?>
				<script>
					files_<?= $name; ?>.push("<?= $file['value']; ?>");
				</script>
				<tr class="template-download fade in">
					<td style="width: 100px;">
						<?php if($file['file_info']['file_type'] == 'image') { ?>
							<span class="preview">
							<a class="fancybox" download="<?= $file['file_info']['name']; ?>" title="<?= $file['file_info']['name']; ?>" href="<?= $file['value']; ?>">
								<img style="max-width: 80px; max-height: 80px;" src="<?= Helpers_File::image2thumb($file['value']); ?>">
							</a>
							</span>
						<?php } ?>
					</td>
					<td>
						<p class="name">
							<a target="_blank" title="<?= $file['file_info']['name']; ?>" href="<?= $file['value']; ?>"><?= $file['file_info']['name']; ?></a>
						</p>
					</td>
					<td>
						<span class="size"><?= $file['file_info']['size']; ?> <?= $file['file_info']['type']; ?></span>
					</td>
					<td>
						<i class="glyphicon glyphicon-trash"></i>
						<button class="btn btn-danger delete" data-url="<?= $file['value']; ?>" data-type="DELETE">
							<i class="glyphicon glyphicon-trash"></i> Удалить
						</button>
					</td>
			</tr>
			<?php } ?>
		<? } ?>
		</tbody>
	</table>
</div>
<script>
	$(function () {
		$("input[name=<?= $name; ?>]").val(files_<?= $name; ?>);

		$('#fileupload_<?= $name; ?>').fileupload({
			url: '/<?= Kohana::$config->load('extasy.admin_path_prefix') . 'file-uploader'; ?>',
			maxFileSize: 25000000,
			previewCrop: true,
			destroy: function(e, data) {
				if (confirm('Вы уверены?')) {
					$.each(files_<?= $name; ?>, function(index, file){
						if (file == data.url.split('?file=').join('files/')) {
							delete files_<?= $name; ?>[index];
						}
					})
					$(data.context).remove();
					$("input[name=<?= $name; ?>]").val(files_<?= $name; ?>);
				}
			},
			success: function(data) {
				$.each(data.files, function(index, file){
					files_<?= $name; ?>.push(file.url);
				})

				$("input[name=<?= $name; ?>]").val(files_<?= $name; ?>);
			}
		});
	});
</script>
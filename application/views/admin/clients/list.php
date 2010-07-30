<script type="text/javascript">
	$(function() {
		$(".data-table tbody tr.client td:not(:last-child)").click(function() {
			document.location.href = "<?php echo url::site('admin/clients/edit'); ?>/" + $(this).parent("tr").attr('data-id');
		});
		
		$(".data-table tr:not(.no-hover) td").hover(function() {
			$(this).parent("tr").find("td").addClass("hover");
		}, function () {
			$(this).parent("tr").find("td").removeClass("hover");
		})
	});
</script>

<table class="data-table">
	<thead>
		<tr>
			<th>Name</th>
			<th width="25%">Email</th>
			<th width="15%">Income</th>
			<th width="120">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		
		if(isset($clients) && is_array($clients) && count($clients) > 0)
		{
			foreach($clients as $client)
			{
				?>
				<tr class="client" data-id="<?php echo $client['client_id']; ?>">
					<td class="first">
						<?php echo $client['client_first_name'] . " " . $client['client_last_name']; ?>
					</td>
					<td><?php echo '<a href="mailto:'.$client['client_email'].'">'.$client['client_email'].'</a>'; ?></td>
					<td><?php echo '---'; ?></td>
					<td>
						<a href="<?php echo url::site('admin/clients/edit/'.$client['client_id']); ?>">Edit</a> &bull;
						<a href="#">Delete</a>
					</td>
				</tr>
			<?php
			}
		}
		else
		{
			?>
			<tr class="">
				<td colspan="4" style="text-align:center;">You do not have any clients currently.</td>
			</tr>
			
			<?php
		}
		?>
	</tbody>
</table>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">


<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>


<div class="container">
           <br><br><br>
  <table id="table_id" class="display">
    <thead>
      <tr>
        <th>#</th>
        <th>Tx Type</th>
        <th>Credit/Debit</th>
        <th>Value</th>
        <th>Created At</th>
      </tr>
    </thead>
    <tbody>
        <?php foreach ($user_tx as $key => $t) : ?>
      <tr>
        <td><?php echo $key+1; ?></td>
        <td><?php echo $t->comment; ?></td>
        <td><?php if($t->value < 0){echo "Debit";} else{echo "Credit";}?></tb>
        <td><?php echo abs($t->value); ?></td>
        <td><?php echo $t->created_at; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
</table>
</div>
<script>
$(document).ready( function () {
    $('#table_id').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
    } );
});

</script>
</body>
</html>

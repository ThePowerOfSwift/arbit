<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>

<style>
.dateDiv
{
    display: flex;
    justify-content: center;
    align-items: center;
}
.toggleAnchor
{
    cursor: pointer;
    color: darkgoldenrod !important;
    font-size: 16px;
}
.annocHeading
{
    border: 2px solid black;
    color: darkgoldenrod;
    text-align: center;
    border-radius: 5px;
}
.annocDiv
{
    font-size:14px;
}
</style>
<div class="row rowDataBot2 margin_Top_50">
    <div class="col-md-4">
    </div>
    <div class="col-md-4 annocHeading">
        <h2>Announcements</h2>
    </div>
    <div class="col-md-4">
    </div>
</div>

<div class="container-fluid">
    <?php foreach ($announcements as $key => $a) : ?>
        <?php $sbj = $a->subject;
                $annc = $a->announcement;
                $small = substr($annc, 0, 200);
        ?>
        <div class="row">
            <div class="col-lg-2 dateDiv">
                <p><?php echo $a->created_at; ?></p>
            </div>
            <div class="col-md-10">
                <h4><b><?php echo $a->subject; ?></b></h4>
                <span class="annocDiv" id="small<?php echo $key; ?>"><?php echo $small."... "; ?></span>
                <p id="text<?php echo $key; ?>" class="annocDiv" style="display:none;"><?php echo $a->announcement; ?></p>
                <a id="toggle<?php echo $key; ?>" class="toggleAnchor" onclick=toggl(<?php echo $key; ?>)>read more</a>
            </div>
        </div>
        <hr>
    <?php endforeach; ?>  
</div>

<!--<div class="container rowDataBot2">-->

<!--    <div class="panel-group" id="accordion">-->
<!--        <?php foreach ($announcements as $key => $a) : ?>-->
<!--        <div class="panel panel-default">-->
<!--            <div class="panel-heading">-->
<!--                <h4 class="panel-title">-->
<!--                <a data-toggle="collapse" data-parent="#accordion" href="<?php echo "#collapse".$key; ?>">-->
<!--                <?php echo $a->subject; ?></a>-->
<!--                </h4>-->
<!--            </div>-->
<!--            <div id="<?php echo "collapse".$key; ?>" class="panel-collapse collapse in">-->
<!--                <div class="panel-body"><?php echo $a->announcement; ?></div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <?php endforeach; ?>-->
<!--    </div>-->

<!--</div>-->


<script>
function toggl(id){
    var elem = $("#toggle"+id).text();
    if (elem == "read more") {
      //Stuff to do when btn is in the read more state
      
      $("#small"+id).hide();
      $("#text"+id).slideDown();
      $("#toggle"+id).text("read less");
      
    } else {
      //Stuff to do when btn is in the read less state
      
      $("#text"+id).slideUp(1, function() {
        $("#small"+id).show();
      });
     
      $("#toggle"+id).text("read more");
    }
}
  
</script>
</body>
</html>

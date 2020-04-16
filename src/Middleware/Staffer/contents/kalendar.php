  <!-- main calendar program -->
  <script type="text/javascript" src="<?= \Middleware\Staffer\AppContext::getPublicDirectory().'kalendar/calendar.js'?>"></script>

  <!-- language for the calendar -->
  <script type="text/javascript" src="<?= \Middleware\Staffer\AppContext::getPublicDirectory().'kalendar/lang/calendar-en.js'?>"></script>

  <!-- the following script defines the Calendar.setup helper function, which makes
       adding a calendar a matter of 1 or 2 lines of code. -->
  <script type="text/javascript" src="<?= \Middleware\Staffer\AppContext::getPublicDirectory().'kalendar/calendar-setup.js'?>"></script>



<table cellspacing="0" cellpadding="0" style="border-collapse: collapse"><tr>
 <td style="font-size:10px">od: <input type="text" name="aktiv_lanstart" id="f_date_a" readonly="1" size="8" value="<?php echo @$zaznam[$db_aktivstart];?>" /></td>
 <td><img src="<?= \Middleware\Staffer\AppContext::getPublicDirectory().'kalendar/img.gif'?>" id="f_trigger_a" style="cursor: pointer; border: 1px solid red;" title="Date selector"
      onmouseover="this.style.background='red';" onmouseout="this.style.background=''" /></td>
 <td>&nbsp;</td>
 <td style="font-size:10px">do: <input type="text" name="aktiv_lanstop" id="f_date_b" readonly="1" size="8" value="<?php echo @$zaznam[$db_aktivstop];?>" /></td>
 <td><img src="<?= \Middleware\Staffer\AppContext::getPublicDirectory().'kalendar/img.gif'?>" id="f_trigger_b" style="cursor: pointer; border: 1px solid red;" title="Date selector"
      onmouseover="this.style.background='red';" onmouseout="this.style.background=''" /></td>
</table>


<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_a",     // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        //ifFormat       :    "%Y-%m-%d %H:%M",       // format of the input field
        showsTime      :    false,
        timeFormat     :    "24",
        button         :    "f_trigger_a",  // trigger for the calendar (button ID)
        align          :    "Tl",           // alignment (defaults to "Bl")
        singleClick    :    true
    });
</script>

<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_b",     // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        //ifFormat       :    "%Y-%m-%d %H:%M",       // format of the input field
        showsTime      :    false,
        timeFormat     :    "24",
        button         :    "f_trigger_b",  // trigger for the calendar (button ID)
        align          :    "Tl",           // alignment (defaults to "Bl")
        singleClick    :    true
    });
</script>

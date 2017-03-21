<?php
/**
 * Created by PhpStorm.
 * User: fritz
 * Date: 3/15/17
 * Time: 2:31 PM
 */?>
<p>
	<label>Primary Care Physician:</label>
	<br>
	<input type="text" name="pcp" value="">
</p>
<p>
    <label>Initials:</label>
    <br>
    <input type="text" name="initials" value="">
</p>
<p>
    <label>Dob:</label>
    <br>
    <input type="text" class="js-datepicker" name="dob" value="">
</p>
<p>
    <label>Serology:</label>
    <br>
    <select name="serology">
        <option value=""></option>
        <option value="0">RF(-) and anti-CCP(-)</option>
        <option value="2">RF(+) or anti-CCP(+)</option>
        <option value="3">High RF(+) or anti-CCP(+)</option>
    </select>
</p>
<p>
    <label>Duration of Symptoms:</label>
    <br>
    <select name="duration">
        <option value=""></option>
        <option value="0"> < 6 weeks </option>
        <option value="1"> >= 6 weeks </option>
    </select>
</p>
<p>
    <label>Acute Phase Reactants:</label>
    <br>
    <select name="apr">
        <option value=""></option>
        <option value="0">CRP and ESR within normal</option>
        <option value="1">elevated CRP or ESR</option>
    </select>
</p>
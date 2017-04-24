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
    <br/>
    Please input date as mm/dd/yyyy
    <br>
    <input type="text" class="" name="dob" value="">
</p>
<p>Negative refers to values that are less than or equal to the upper limit of normal for the laboratory and assay.</p>
<p>Low-positive refers to values that are higher than the upper limit of normal but less than three times the upper limit of normal for the laboratory and assay.</p>
<p>High-positive refers to values that are more than three times the upper limit of normal for the laboratory and assay.</p>
<p>When RF information is only available as positive or negative, a positive result should be scored as low-positive RF.</p>
<p>
    <label>Serology:</label>
    <br>
    <select name="serology">
        <option value=""></option>
        <option value="0">RF(-) and anti-CCP(-)</option>
        <option value="2">Low RF(+) or Low anti-CCP(+)</option>
        <option value="3.5">High RF(+) or High anti-CCP(+)</option>
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
        <option value="0.5">elevated CRP or ESR</option>
    </select>
</p>
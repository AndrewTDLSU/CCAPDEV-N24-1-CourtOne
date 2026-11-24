<?php
$slotStatus = isset($grid[$time][$court['courtID']]) ? 'Booked'
    : (strtotime($targetDate . ' ' . $time) <= time() ? 'Past' : 'Available');
$slotTime = courtTime($time);
?>
<button type="button" class="court-slot <?= strtolower($slotStatus) ?>"
        data-court="<?= (int) $court['courtID'] ?>" data-time="<?= courtEscape($time) ?>"
        data-label="<?= courtEscape($court['courtName'] . ' · ' . courtDate($targetDate) . ' · ' . $slotTime) ?>"
        aria-pressed="false" <?= $slotStatus !== 'Available' ? 'disabled' : '' ?>>
    <span class="<?= $mobileSlot ? 'd-block' : 'visually-hidden' ?>"><?= courtEscape($slotTime) ?></span>
    <span class="visually-hidden"><?= courtEscape($court['courtName']) ?></span>
    <span class="slot-state"><?= $slotStatus ?></span>
</button>

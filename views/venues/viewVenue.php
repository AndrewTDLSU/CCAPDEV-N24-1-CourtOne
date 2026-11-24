<?php
$title = $venue['venueName'];
$pageStyles = ['venueStyles.css'];
$pageScripts = ['integration.js'];
require __DIR__ . '/../partials/Header.php';

$slots = [];
$start = strtotime($venue['openHour']);
$end = strtotime($venue['closeHour']);
if ($end <= $start) {
    $end = strtotime('24:00:00');
}
for ($time = $start; $time + 3600 <= $end; $time += 3600) {
    $slots[] = date('H:i:s', $time);
}
$venueRoute = 'index.php?url=venue/viewVenue/' . (int) $venue['venueID'];
$canReserve = ($_SESSION['role'] ?? '') === 'user';
$staffContacts = array_filter($staffContacts ?? [], fn($member) => (int) $member['userID'] !== (int) ($_SESSION['userID'] ?? 0));
?>

<main class="container my-4" id="main-content">
  <section class="venue-overview" aria-labelledby="venue-name">
    <img class="venue-detail-image" src="<?= courtEscape(courtImage($images[0] ?? $venue)) ?>"
         data-image-fallback="<?= courtEscape(courtImageFallback()) ?>"
         alt="Venue photo for <?= courtEscape($venue['venueName']) ?>">
    <div>
      <p class="text-body-secondary mb-1"><?= courtEscape($venue['sportsType']) ?></p>
      <h1 class="court-page-title mb-2" id="venue-name"><?= courtEscape($venue['venueName']) ?></h1>
      <p class="mb-1"><?= courtEscape($venue['address']) ?></p>
      <p class="mb-3"><?= count($courts) ?> <?= count($courts) === 1 ? 'court' : 'courts' ?><?php if (isset($venue['hourlyRate'])): ?> · ₱<?= number_format($venue['hourlyRate'], 2) ?> / hour<?php endif; ?></p>
      <p class="text-body-secondary mb-0"><?= courtEscape($venue['description']) ?></p>
      <?php if (!empty($staffContacts)): ?>
        <div class="court-staff-contact">
          <?php if (!empty($_SESSION['userID'])): ?>
            <strong>Venue staff</strong>
            <?php foreach ($staffContacts as $member): ?>
              <a class="btn btn-outline-primary btn-sm" href="<?= courtEscape(courtUrl('index.php?url=chat/index&receiverID=' . (int) $member['userID'] . '#message-thread')) ?>">Message <?= courtEscape($member['name']) ?></a>
            <?php endforeach; ?>
          <?php else: ?>
            <a class="btn btn-outline-primary btn-sm" href="<?= courtEscape(courtUrl('index.php?url=auth/login')) ?>">Sign in to message staff</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="mt-4" aria-labelledby="timeslot-title">
    <h2 class="h4" id="timeslot-title">Reservation schedule</h2>
    <?php if (!$courts || !$slots): ?>
      <p class="court-empty"><?= !$courts ? 'This venue has no courts listed yet.' : 'There are no time slots available for this venue.' ?></p>
    <?php else: ?>
      <p class="text-body-secondary">Choose a date and an available one-hour slot. All times are in Philippine time.</p>
      <div class="desktop-schedule card p-3">
        <div class="date-toolbar" aria-label="Booking dates">
          <?php $previous = date('Y-m-d', strtotime($targetDate . ' -5 days')); ?>
          <a class="btn btn-outline-secondary" aria-label="Previous five dates" href="<?= courtEscape(courtUrl($venueRoute . '&date=' . $previous)) ?>">‹</a>
          <?php for ($offset = 0; $offset < 5; $offset++): ?>
            <?php $date = date('Y-m-d', strtotime($targetDate . ' +' . $offset . ' days')); ?>
            <a class="btn <?= $offset === 0 ? 'btn-primary' : 'btn-outline-secondary' ?>"
               <?= $offset === 0 ? 'aria-current="date"' : '' ?>
               href="<?= courtEscape(courtUrl($venueRoute . '&date=' . $date)) ?>"><?= date('D, M j', strtotime($date)) ?></a>
          <?php endfor; ?>
          <?php $next = date('Y-m-d', strtotime($targetDate . ' +5 days')); ?>
          <a class="btn btn-outline-secondary" aria-label="Next five dates" href="<?= courtEscape(courtUrl($venueRoute . '&date=' . $next)) ?>">›</a>
        </div>
        <div class="table-scroll" tabindex="0" role="region" aria-label="Court availability">
          <table class="table availability-table align-middle text-center mb-0">
            <thead>
              <tr><th scope="col">Time</th>
                <?php foreach ($courts as $court): ?><th scope="col"><?= courtEscape($court['courtName']) ?></th><?php endforeach; ?>
              </tr>
            </thead>
            <tbody>
              <?php $mobileSlot = false; ?>
              <?php foreach ($slots as $time): ?>
                <tr><th scope="row"><?= courtTime($time) ?></th>
                  <?php foreach ($courts as $court): ?>
                    <td><?php require __DIR__ . '/../partials/TimeSlot.php'; ?></td>
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="mobile-date-list">
        <form class="d-flex align-items-end gap-2 mb-3" method="get" action="<?= courtEscape(courtUrl('index.php')) ?>">
          <input type="hidden" name="url" value="venue/viewVenue/<?= (int) $venue['venueID'] ?>">
          <div>
            <label for="mobile-date" class="form-label">Booking date</label>
            <input class="form-control" id="mobile-date" type="date" name="date" value="<?= courtEscape($targetDate) ?>" required>
          </div>
          <button class="btn btn-outline-secondary" type="submit">Go</button>
        </form>
        <?php $mobileSlot = true; ?>
        <?php foreach ($courts as $index => $court): ?>
          <details class="card p-3 mb-2" <?= $index === 0 ? 'open' : '' ?>>
            <summary class="fw-semibold"><?= courtEscape($court['courtName']) ?></summary>
            <div class="mobile-slot-list mt-3">
              <?php foreach ($slots as $time): ?>
                <?php require __DIR__ . '/../partials/TimeSlot.php'; ?>
              <?php endforeach; ?>
            </div>
          </details>
        <?php endforeach; ?>
      </div>

      <?php if ($canReserve): ?>
        <form class="reservation-actions" action="<?= courtEscape(courtUrl('index.php?url=booking/reserve')) ?>" method="post">
          <div>
            <input type="hidden" name="date" value="<?= courtEscape($targetDate) ?>">
            <input type="hidden" name="courtID" id="selected-court">
            <input type="hidden" name="time" id="selected-time">
            <input type="hidden" name="csrfToken" value="<?= courtEscape($_SESSION['csrfToken']) ?>">
            <p id="booking-summary" class="mb-0" aria-live="polite">Select an available time slot.</p>
          </div>
          <button id="reserve-button" class="btn btn-primary" type="submit" disabled>Reserve selected slot</button>
        </form>
      <?php else: ?>
        <p id="booking-summary" class="court-empty mt-3 mb-0" aria-live="polite">Select an available time slot.</p>
      <?php endif; ?>
      <?php if (!$canReserve && empty($_SESSION['userID'])): ?>
        <a class="btn btn-primary mt-3" href="<?= courtEscape(courtUrl('index.php?url=auth/login')) ?>">Sign in to reserve</a>
      <?php elseif (!$canReserve): ?>
        <p class="text-body-secondary mt-2">Reservations are made from customer accounts.</p>
      <?php endif; ?>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/../partials/Footer.php'; ?>

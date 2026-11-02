<table>
    <thead>
        <tr>
            <th>Time Slot</th>
            <?php foreach ($courts as $court): ?>
                <th><?php echo htmlspecialchars($court['name'], ENT_QUOTES, 'UTF-8'); ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        // Convert operational hours to structural loop integer timestamps
        $start = strtotime($venue['openHour']);
        $end   = strtotime($venue['closeHour']);

        // Step through hours sequentially from open to close
        for ($current = $start; $current < $end; $current = strtotime('+1 hour', $current)) {
            $timeSlot = date('H:i', $current);
            ?>
            <tr>
                <!-- Row Header showing the time block -->
                <td><strong><?php echo date('h:i A', $current); ?></strong></td>
                
                <?php foreach ($courts as $court): ?>
                    <td>
                        <?php 
                        // Quick lookup using our formatted array map
                        if (isset($bookingMap[$court['id']][$timeSlot])) {
                            echo '<span class="status-booked">Booked</span>';
                        } else {
                            echo '<span class="status-available">Available</span>';
                        }
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

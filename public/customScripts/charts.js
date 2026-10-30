// ------------------------------- revenue line chart ----------------------------------
// context
const revenuectx = document.getElementById('revenueLineChart').getContext('2d');

// data
const revenueLineChartData = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
  datasets: [
    {
      label: 'Revenue',
      data: [10000, 30000, 20000, 60000, 50000, 40000], 
      borderColor: 'blue',
      backgroundColor: 'rgba(0, 68, 255, 0.5)', 
      pointStyle: 'circle',
      pointRadius: 6,
      pointHoverRadius: 10,
    }
  ]
};

// config
const revenueLineChartConfig = {
  type: 'line',
  data: revenueLineChartData, // pass the data defined above
  options: {
    responsive: true,
    maintainAspectRatio: false, // to place inside a sized container
    plugins: {
      title: {
        display: true,
        text: 'Monthly Revenue Overview' 
      }
    }
  }
};

// initialize chart
const revenueLineChart = new Chart(revenuectx, revenueLineChartConfig);



// ------------------------------- bookings bar graph ----------------------------------
// context
const bookingsBarCtx = document.getElementById('bookingsBarGraph').getContext('2d');

// data 
const bookingsBarGraphData = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], 
  datasets: [
    {
      label: 'Bookings',
      data: [150, 250, 100, 175, 215, 200], 
      borderColor: 'blue',
      backgroundColor: 'rgba(0, 68, 255, 0.5)', 
      borderWidth: 1
    },
  ]
};

// config
const bookingsBarGraphConfig = {
  type: 'bar', 
  data: bookingsBarGraphData,
  options: {
    responsive: true,
    maintainAspectRatio: false, 
    plugins: {
      legend: {
        position: 'top',
      },
      title: {
        display: true,
        text: 'Monthly Bookings Overview'
      }
    }
  },
};

// initialize chart
const bookingsBarGraph = new Chart(bookingsBarCtx, bookingsBarGraphConfig);



// ------------------------------- court usage per venue pie chart ----------------------------------
// context
const courtUsageCtx = document.getElementById('courtUsagePieChart').getContext('2d');

// setup
const courtUsagePieChartData = {
  labels: ['Court 1', 'Court 2', 'Court 3'],
  datasets: [
    {
      label: 'Dataset 1',
      data: [10, 20, 30],
      borderColor: 'white',
      backgroundColor: [
        'rgba(255, 99, 132, 0.7)',
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 206, 86, 0.7)'
      ],
      borderWidth: 1
    }
  ]
};

// config
const courtUsagePieChartConfig = {
  type: 'pie',
  data: courtUsagePieChartData,
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'top',
      },
      title: {
        display: true,
        text: 'Manila Basketball Court (January 2026)'
      }
    }
  },
};

// initialize chart
const courtUsagePieChart = new Chart(courtUsageCtx, courtUsagePieChartConfig);
<?php
ob_start();
include 'header.php'; // Include your header file
include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report</title>
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #pieChart {
            width: 400px !important;
            height: 400px !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1>Generate Report</h1>

                <!-- Filter by Year -->
                <div class="form-group">
                    <label for="year">Filter by Year:</label>
                    <input type="text" id="year" name="year" class="form-control" placeholder="Enter year">
                </div>

                <!-- Button to generate the pie chart -->
                <button class="btn btn-primary mb-3" onclick="generatePieChart()">Generate Pie Chart</button>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Canvas for pie chart -->
                <div class="text-center">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Table to display aggregated data (initially hidden) -->
                <div id="tableContainer" class="mt-4" style="display: none;">
                    <h2 class="text-center">Aggregated Data</h2>
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Category</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Placeholder for error message -->
        <div id="errorMessage" class="text-danger mt-4"></div>
    </div>

    <script>
        // Function to generate the pie chart and show the table
        function generatePieChart() {
            // Get the year value from the input
            var year = document.getElementById('year').value;

            // Fetch available years from the server
            fetch('get_available_years.php')
                .then(response => response.json())
                .then(availableYears => {
                    // Check if the entered year is in the available years
                    if (!availableYears.includes(year)) {
                        // Display error message
                        document.getElementById('errorMessage').innerText = 'Invalid year. Available years: ' + availableYears.join(', ');
                        return;
                    }

                    // Clear any previous error message
                    document.getElementById('errorMessage').innerText = '';

                    // Fetch data from get_chart_data.php with the selected year
                    fetch('get_chart_data.php?year=' + year)
                        .then(response => response.json())
                        .then(data => {
                            // Prepare data for the pie chart
                            var labels = data.map(item => item.category);
                            var values = data.map(item => item.total);

                            // Draw the pie chart
                            var ctx = document.getElementById('pieChart').getContext('2d');
                            var myPieChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        data: values,
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.7)',
                                            'rgba(54, 162, 235, 0.7)',
                                            'rgba(255, 206, 86, 0.7)',
                                            'rgba(75, 192, 192, 0.7)',
                                            'rgba(153, 102, 255, 0.7)',
                                            'rgba(255, 159, 64, 0.7)'
                                        ],
                                        borderWidth: 1
                                    }]
                                }
                            });

                            // Show the table container
                            document.getElementById('tableContainer').style.display = 'block';

                            // Update the table with aggregated data
                            updateTable(data);
                        })
                        .catch(error => console.error('Error:', error));
                })
                .catch(error => console.error('Error:', error));
        }

        // Function to update the table with aggregated data
        function updateTable(data) {
            var tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';

            data.forEach(item => {
                var row = `<tr>
                                <td>${item.category}</td>
                                <td>${item.total}</td>
                            </tr>`;
                tableBody.innerHTML += row;
            });
        }
    </script>
</body>

<?php
ob_end_flush();
include 'footer.php'; // Include your footer file
?>

</html>
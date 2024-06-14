// Define the colors for the pie chart
const primaryColor = '#4834d4';
const warningColor = '#f0932b';
const successColor = '#6ab04c';
const dangerColor = '#eb4d4b';

// Get the table body element that contains the data
var tbody = document.getElementById('chart-facilitate');
console.log(tbody);
var rows = tbody.getElementsByTagName("tr");

// Initialize arrays to store prices and dates
var prices = [];
var dates = [];

// Loop through the table rows to extract the data
for (var index = 0; index < rows.length; index++) { 
    prices.push(parseFloat(rows[index].children[2].innerText.substring(2).replace(/,/g, '')));
    dates.push(rows[index].children[3].innerText); 
} 

// Prepare the data for the pie chart
var data = {
    labels: dates,
    datasets: [{
        backgroundColor: [
            primaryColor,
            warningColor,
            successColor,
            dangerColor,
            // Add more colors if necessary
        ],
        label: 'Expenses',
        data: prices,
    }]
};

// Get the canvas element for the chart
var ctx = document.getElementById('myChart').getContext('2d');

// Create the pie chart
var pieChart = new Chart(ctx, {
    type: 'pie',
    data: data,
    options: {
        maintainAspectRatio: false,
    }
});

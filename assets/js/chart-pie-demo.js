// Set new default font family and font color to mimic Bootstrap's default styling
(Chart.defaults.global.defaultFontFamily = "Nunito"),
  '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = "#858796";

// Pie Chart Example
var ctx = document.getElementById("myPieChart");
var numarMedici = parseInt(ctx.getAttribute("data-numar-medici"));
var numarPacienti = parseInt(ctx.getAttribute("data-numar-pacienti"));
var numarArticole = parseInt(ctx.getAttribute("data-numar-articole"));

var myPieChart = new Chart(ctx, {
  type: "doughnut",
  data: {
    labels: ["Nr", "Nr", "Nr"],
    datasets: [
      {
        data: [numarMedici, numarPacienti, numarArticole],
        backgroundColor: ["#4e73df", "#1cc88a", "#f0ad4e"],
        hoverBackgroundColor: ["#2e59d9", "#17a673", "#d89c46"],
        hoverBorderColor: "rgba(234, 236, 244, 1)",
      },
    ],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: "#dddfeb",
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
    legend: {
      display: false,
    },
    cutoutPercentage: 80,
  },
});

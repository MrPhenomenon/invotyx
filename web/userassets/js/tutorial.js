const tutorials = {
  dashboard: [
    {
      element: "#search-mcq",
      popover: {
        title: "MCQ Search",
        description:
          "Use this search bar to quickly find specific MCQs by keywords, topics, or questions.",
      },
    },
    {
      element: "#sidebar",
      popover: {
        title: "Sidebar Menu",
        description:
          "This is your main navigation menu. Access different sections like Dashboard, Analytics, Study Plan, Exams, MCQs, and Profile from here.",
      },
    },
    {
      element: ".pc-item.dashboard",
      popover: {
        title: "Dashboard",
        description:
          "Your central hub — get an overview of your performance, quick actions, and study progress all in one place.",
      },
    },
    {
      element: ".pc-item.analytics",
      popover: {
        title: "Analytics",
        description:
          "Dive deeper into your learning data — view progress breakdown by chapters and topics.",
      },
    },
    {
      element: ".pc-item.study-plan",
      popover: {
        title: "Study Plan",
        description:
          "Access your personalized study plan. Follow daily targets and track your completion status here.",
      },
    },
    {
      element: ".pc-item.exam",
      popover: {
        title: "Start New Exam",
        description:
          "Begin a new exam session to test your knowledge and practice questions from selected subjects or organ systems.",
      },
    },
    {
      element: ".pc-item.results",
      popover: {
        title: "Exam History",
        description:
          "Review your previous exams, check scores, and analyze detailed question-by-question results.",
      },
    },
    {
      element: ".pc-item.bookmarks",
      popover: {
        title: "Bookmarked MCQs",
        description:
          "Revisit the questions you’ve bookmarked for later review — perfect for focused revisions.",
      },
    },
    {
      element: ".pc-item.profile",
      popover: {
        title: "Profile Settings",
        description:
          "Manage your account, adjust preferences, and update personal details from this page. You can start the tutorial again from here.",
      },
    },

    {
      element: "#accuracy-chart",
      popover: {
        title: "Accuracy Chart",
        description:
          "This chart shows your performance accuracy over different days, helping you track your progress and consistency.",
      },
    },
    {
      element: "#quick-actions",
      popover: {
        title: "Quick Actions",
        description:
          "Access key actions here — start your daily study plan, resume an ongoing exam, or review past exams quickly from this section.",
      },
    },
    {
      element: ".dashboard-card .bg-light.border.rounded-3",
      popover: {
        title: "Today's Study Plan",
        description:
          "See an overview of today’s study session including total MCQs, subjects, and chapters covered. You can start or resume your exam here.",
      },
    },

    {
      element: "#performance",
      popover: {
        title: "Performance Overview",
        description:
          "This section gives you a quick summary of your study progress — including how many questions you've attempted, your accuracy, and completed exams — all at a glance.",
      },
    },

    {
      element: "#exam-overview .col-lg-6:first-child",
      popover: {
        title: "Active Exams",
        description:
          "This section lists your ongoing exams. You can check your progress and resume any unfinished session directly from here.",
      },
    },
    {
      element: "#exam-overview .col-lg-6:last-child",
      popover: {
        title: "Recent Completed Exams",
        description:
          "View your most recent completed exams along with accuracy stats and quick access to detailed results.",
      },
    },
  ],
  exam: [
    {
      element: "#progress-bar",
      popover: {
        title: "Progress",
        description: "Tracks how far you’ve gone.",
      },
    },
    {
      element: "#submit-btn",
      popover: {
        title: "Submit",
        description: "Click here to finish your exam.",
      },
    },
  ],
};

$(function () {
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get("tutorial") === "1") {
    const driverObj = window.driver.js.driver;

    const driver = driverObj({
      showProgress: true,
      steps: tutorials.dashboard.filter((step) =>
        $(step.element).is(":visible")
      ),
    });

    driver.drive();
  }
});

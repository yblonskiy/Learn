using Microsoft.AspNetCore.Mvc;

namespace DayBook.Web.Controllers
{
    public class HomeController : Controller
    {
        public IActionResult Index() => View();
    }
}
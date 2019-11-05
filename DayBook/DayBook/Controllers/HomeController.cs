using ApplicationCore.Interfaces;
using DayBook.Web.ViewModels.Record;
using Infrastructure.Identity;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;
using System.Linq;

namespace DayBook.Web.Controllers
{
    [Authorize]
    public class HomeController : Controller
    {       
        public HomeController()
        {
            
        }

        public IActionResult Index()
        {
            return View();
        }
    }
}
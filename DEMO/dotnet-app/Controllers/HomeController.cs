using Microsoft.AspNetCore.Mvc;

namespace DotnetDemo.Controllers
{
    public class HomeController : Controller
    {
        public IActionResult Index()
        {
            return View();
        }

        public IActionResult Contrast()
        {
            return View();
        }

        public IActionResult Error()
        {
            return View();
        }
    }
}

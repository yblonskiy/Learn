using ApplicationCore.Entities;
using ApplicationCore.Interfaces;
using DayBook.Application.Interfaces;
using DayBook.Web.ViewModels.Record;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using System;
using System.Linq;
using System.Threading.Tasks;

namespace DayBook.Web.Controllers
{
    [Authorize]
    public class RecordController : Controller
    {
        private readonly IAccountService _accountService;        
        private readonly IRepository<Record> _recordRepository;

        int pageSize = 5;

        public RecordController(IRepository<Record> recordRepository,
            IAccountService accountService)
        {
            _recordRepository = recordRepository;
            _accountService = accountService;
        }

        public async Task<IActionResult> Index(string currentFilter, string searchString, int? pageNumber)
        {
            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                throw new ApplicationException($"Unable to load user.");
            }

            if (searchString != null)
            {
                pageNumber = 1;
            }
            else
            {
                searchString = currentFilter;
            }

            ViewData["CurrentFilter"] = searchString;

            var records = _recordRepository.FindBy(r => r.UserId == user.Id);

            if (!string.IsNullOrEmpty(searchString))
            {
                records = records.Where(s => s.Title.Contains(searchString, StringComparison.CurrentCultureIgnoreCase)
                                       || s.Body.Contains(searchString, StringComparison.CurrentCultureIgnoreCase));
            }

            var recordsViewModel = records
                .Select(r => new RecordViewModel()
                {
                    Id = r.Id,
                    Title = r.Title,
                    DateCreated = r.DateCreated
                }).AsQueryable();
                       
            return View(PaginatedList<RecordViewModel>.Create(recordsViewModel, pageNumber ?? 1, pageSize));
        }

        // GET: /Record/Create
        [HttpGet]
        public ActionResult Create()
        {
            return View();
        }

        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create(RecordViewModel model)
        {
            if (!ModelState.IsValid)
            {
                return BadRequest(ModelState);
            }

            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                throw new ApplicationException($"Unable to load user.");
            }

            var record = new Record()
            {
                UserId = user.Id,
                Title = model.Title,
                DateCreated = DateTime.Now,
                Body = model.Body
            };

            await _recordRepository.AddAsync(record);

            return RedirectToAction("Index", "Record");
        }

        // GET: Record/Details/XXX
        public async Task<IActionResult> Details(string id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                throw new ApplicationException($"Unable to load user.");
            }

            var record = await _recordRepository.GetSingleAsync(r => r.Id.ToLower() == id.ToLower() && r.UserId == user.Id);

            if (record == null)
            {
                return NotFound();
            }

            var model = new RecordViewModel
            {
                Id = record.Id,
                Title = record.Title,
                DateCreated = record.DateCreated,
                Body = record.Body
            };

            return View(model);
        }

        // GET: Record/Edit/XXX
        public async Task<IActionResult> Edit(string id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                throw new ApplicationException($"Unable to load user.");
            }

            var record = await _recordRepository.GetSingleAsync(r => r.Id.ToLower() == id.ToLower() && r.UserId == user.Id);

            if (record == null)
            {
                return NotFound();
            }

            var model = new RecordViewModel
            {
                Id = record.Id,
                Title = record.Title,
                DateCreated = record.DateCreated,
                Body = record.Body
            };

            return View(model);
        }

        // POST: Record/Edit/XXX
        [HttpPost, ActionName("Edit")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> EditPost(string id, RecordViewModel model)
        {
            if (id == null)
            {
                return NotFound();
            }

            if (!ModelState.IsValid)
            {
                return BadRequest(ModelState);
            }

            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                throw new ApplicationException($"Unable to load user.");
            }

            var updateRecord = await _recordRepository.GetSingleAsync(r => r.Id == id && r.UserId == user.Id);

            if (updateRecord == null)
            {
                return NotFound();

            }

            updateRecord.Title = model.Title;
            updateRecord.Body = model.Body;

            try
            {
                await _recordRepository.UpdateAsync(updateRecord);

                return RedirectToAction(nameof(Index));
            }
            catch (Exception ex)
            {
                ModelState.AddModelError("", "Unable to save changes. " +
                    "Try again, and if the problem persists, " +
                    "see your system administrator.");
            }

            return View(new RecordViewModel
            {
                Id = updateRecord.Id,
                Title = updateRecord.Title,
                DateCreated = updateRecord.DateCreated,
                Body = updateRecord.Body
            });
        }

        // GET: Record/Delete/XXX
        public async Task<IActionResult> Delete(string id, bool? saveChangesError = false)
        {
            if (id == null)
            {
                return NotFound();
            }

            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                throw new ApplicationException($"Unable to load user.");
            }

            var record = await _recordRepository.GetSingleAsync(r => r.Id == id && r.UserId == user.Id);

            if (record == null)
            {
                return NotFound();
            }

            if (record.DateCreated < DateTime.Now.AddDays(-2))
            {
                return BadRequest("You can't delete this record!");
            }

            if (saveChangesError.GetValueOrDefault())
            {
                ViewData["ErrorMessage"] =
                    "Delete failed. Try again, and if the problem persists " +
                    "see your system administrator.";
            }

            return View(new RecordViewModel
            {
                Id = record.Id,
                Title = record.Title,
                DateCreated = record.DateCreated,
                Body = record.Body
            });
        }

        // POST: Record/Delete/XXX
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(string id)
        {
            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                throw new ApplicationException($"Unable to load user.");
            }

            var record = await _recordRepository.GetSingleAsync(r => r.Id == id && r.UserId == user.Id);

            if (record == null)
            {
                return NotFound();
            }

            if (record.DateCreated < DateTime.Now.AddDays(-2))
            {
                return BadRequest("You can't delete this record!");
            }

            try
            {
                await _recordRepository.DeleteAsync(record);

                return RedirectToAction(nameof(Index));
            }
            catch (Exception ex)
            {
                return RedirectToAction(nameof(Delete), new { id = id, saveChangesError = true });
            }
        }
    }
}
﻿using ApplicationCore.Entities;
using AutoMapper;
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
    [Route("[controller]/[action]/{id?}")]
    public class RecordController : Controller
    {
        private readonly IMapper _mapper;

        private readonly IAccountService _accountService;
        private readonly IRecordService _recordService;

        int pageSize = 5;

        public RecordController(IAccountService accountService,
            IRecordService recordService,
            IMapper mapper)
        {
            _mapper = mapper;

            _accountService = accountService;
            _recordService = recordService;
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

            var records = _recordService.ListByUserIdAsync(user.Id);

            if (!string.IsNullOrEmpty(searchString))
            {
                records = records.Where(s => s.Title.Contains(searchString, StringComparison.CurrentCultureIgnoreCase)
                                       || s.Body.Contains(searchString, StringComparison.CurrentCultureIgnoreCase));
            }

            var recordsViewModel = records
                .OrderByDescending(o => o.DateCreated)
                .Select(r => new RecordViewModel()
                {
                    Id = r.Id,
                    Title = r.Title,
                    DateCreated = r.DateCreated
                })
                .AsQueryable();

            return View(PaginatedList<RecordViewModel>.Create(recordsViewModel, pageNumber ?? 1, pageSize));
        }

        // GET: /Record/Create
        [HttpGet]
        public ActionResult Create() => View();

        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create(RecordViewModel model)
        {
            if (!ModelState.IsValid)
            {
                return View(model);
            }

            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                ModelState.AddModelError(string.Empty, "Unable to load user.");
                return View(model);
            }

            var record = new Record()
            {
                UserId = user.Id,
                Title = model.Title,
                DateCreated = DateTime.Now,
                Body = model.Body
            };

            var result = await _recordService.AddAsync(record);

            if (!result.Success)
            {
                ModelState.AddModelError(string.Empty, result.Message);
                return View(model);
            }

            return RedirectToAction("Index");
        }

        // GET: Record/Details/XXX
        [HttpGet]
        public async Task<IActionResult> Details(string id)
        {
            if (id == null)
            {
                return BadRequest();
            }

            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                ModelState.AddModelError(string.Empty, "Unable to load user.");
                return View();
            }

            var record = await _recordService.GetSingleByUserIdAsync(id, user.Id);

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
        [HttpGet]
        public async Task<IActionResult> Edit(string id)
        {
            if (id == null)
            {
                return BadRequest();
            }

            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                ModelState.AddModelError(string.Empty, "Unable to load user.");
                return View();
            }

            var record = await _recordService.GetSingleByUserIdAsync(id, user.Id);

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

        // POST: Record/Edit
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(RecordViewModel model)
        {
            if (!ModelState.IsValid)
            {
                return View(model);
            }

            var record = _mapper.Map<RecordViewModel, Record>(model);
            var result = await _recordService.UpdateAsync(model.Id, record);

            if (!result.Success)
            {
                ModelState.AddModelError(string.Empty, result.Message);
                return View(model);
            }

            return RedirectToAction("Index");
        }

        // GET: Record/Delete/XXX
        [HttpGet]
        public async Task<IActionResult> Delete(string id)
        {
            if (id == null)
            {
                return BadRequest();
            }

            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                ModelState.AddModelError(string.Empty, "Unable to load user.");
                return View();
            }

            var record = await _recordService.GetSingleByUserIdAsync(id, user.Id);

            if (record == null)
            {
                return NotFound();
            }

            if (record.DateCreated < DateTime.Now.AddDays(-2))
            {
                ModelState.AddModelError(string.Empty, "You can't delete this record!");
                return View();
            }

            var view = _mapper.Map<Record, RecordViewModel>(record);
            return View(view);

            //return View(new RecordViewModel
            //{
            //    Id = record.Id,
            //    Title = record.Title,
            //    DateCreated = record.DateCreated,
            //    Body = record.Body
            //});
        }

        // POST: Record/Delete/XXX
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirm(string id)
        {
            if (id == null)
            {
                return BadRequest();
            }

            var result = await _recordService.DeleteAsync(id);

            if (!result.Success)
            {
                ModelState.AddModelError(string.Empty, result.Message);
                return View();
            }

            return RedirectToAction("Index");
        }
    }
}
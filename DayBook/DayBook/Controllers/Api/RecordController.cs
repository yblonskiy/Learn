using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using ApplicationCore.Entities;
using AutoMapper;
using DayBook.Application.Interfaces;
using DayBook.Web.ViewModels.Record;
using Microsoft.AspNetCore.Authorization;

namespace DayBook.Web.Controllers.Api
{
    [Authorize]
    [Route("api/[controller]")]
    [ApiController]
    public class RecordController : ControllerBase
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

        // GET: api/Record
        [HttpGet]
        public async Task<IActionResult> GetAll(string search, int? pageNumber)
        {
            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                throw new ApplicationException($"Unable to load user.");
            }
                     
            var records = _recordService.ListByUserIdAsync(user.Id);

            if (!string.IsNullOrEmpty(search))
            {
                records = records.Where(s => s.Title.Contains(search, StringComparison.CurrentCultureIgnoreCase)
                                       || s.Body.Contains(search, StringComparison.CurrentCultureIgnoreCase));
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

            return Ok(PaginatedList<RecordViewModel>.Create(recordsViewModel, pageNumber ?? 1, pageSize));
        }

        // GET: api/Record/5
        [HttpGet("{id}")]
        public async Task<IActionResult> GetRecord(string id)
        {
            if (id == null)
            {
                return BadRequest();
            }

            var user = await _accountService.GetUserAsync(User.Identity.Name);
            if (user == null)
            {
                ModelState.AddModelError(string.Empty, "Unable to load user.");
                return NotFound();
            }

            var record = await _recordService.GetSingleByUserIdAsync(id, user.Id);

            if (record == null)
            {
                return NotFound();
            }

            var model = _mapper.Map<Record, RecordViewModel>(record);

            return Ok(model);
        }

        // POST: api/Record
        [HttpPost]
        public void Post([FromBody] string value)
        {
        }

        // PUT: api/Record/5
        [HttpPut("{id}")]
        public void Put(int id, [FromBody] string value)
        {
        }

        // DELETE: api/ApiWithActions/5
        [HttpDelete("{id}")]
        public void Delete(int id)
        {
        }
    }
}

using ApplicationCore.Entities;
using AutoMapper;
using DayBook.Application.Interfaces;
using DayBook.Web.ViewModels.Record;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace DayBook.Web.Controllers.Api
{
    [Authorize(Policy = "ApiUser")]
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
        public IActionResult GetAll(string search, int? pageNumber)
        {
            var userId = User.Claims.Single(c => c.Type == "id");

            var records = _recordService.ListByUserId(userId.Value);

            if (!string.IsNullOrEmpty(search))
            {
                records = records.Where(s => s.Title.Contains(search, StringComparison.CurrentCultureIgnoreCase)
                                       || s.Body.Contains(search, StringComparison.CurrentCultureIgnoreCase));
            }

            var recordsDtos = _mapper.Map<IList<RecordViewModel>>(records
                .OrderByDescending(o => o.DateCreated));

            return Ok(PaginatedList<RecordViewModel>.Create(recordsDtos.ToList().AsQueryable(), pageNumber ?? 1, pageSize));
        }

        // GET: api/Record/5
        [HttpGet("{id}")]
        public async Task<IActionResult> GetRecordAsync(string id)
        {
            if (id == null)
            {
                return BadRequest();
            }

            var record = await _recordService.GetSingleByUserIdAsync(id, User.Identity.Name);

            if (record == null)
            {
                return NotFound();
            }

            var model = _mapper.Map<Record, RecordViewModel>(record);

            return Ok(model);
        }

        // POST: api/Record
        [HttpPost]
        public async Task<IActionResult> CreateAsync([FromBody] RecordViewModel model)
        {
            var record = new Record()
            {
                UserId = User.Identity.Name,
                Title = model.Title,
                DateCreated = DateTime.Now,
                Body = model.Body
            };

            var result = await _recordService.AddAsync(record);

            if (!result.Success)
            {
                return BadRequest(new { message = result.Message });
            }

            return Ok();
        }

        // PUT: api/Record/5
        [HttpPut("{id}")]
        public async Task<IActionResult> EditAsync(string id, [FromBody] RecordViewModel model)
        {
            if (id == null)
            {
                return BadRequest();
            }
            
            var record = await _recordService.GetSingleByUserIdAsync(id, User.Identity.Name);

            if (record == null)
            {
                return NotFound();
            }

            return Ok();
        }

        // DELETE: api/record/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteAsync(string id)
        {
            if (id == null)
            {
                return BadRequest();
            }

            var result = await _recordService.DeleteAsync(id);

            if (!result.Success)
            {
                return BadRequest(new { message = result.Message });
            }

            return Ok();
        }
    }
}

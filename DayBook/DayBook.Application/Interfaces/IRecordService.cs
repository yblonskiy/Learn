using Infrastructure.Identity;
using System.Collections.Generic;
using System.Threading.Tasks;
using ApplicationCore.Entities;
using DayBook.Application.Communication;

namespace DayBook.Application.Interfaces
{
    public interface IRecordService
    {
        IEnumerable<Record> ListByUserId(string userId);

        Task<Record> GetSingleByUserIdAsync(string id, string userId);

        Task<RecordResponse> AddAsync(Record record);

        Task<RecordResponse> UpdateAsync(string id, Record record);

        Task<RecordResponse> DeleteAsync(string id);
    }
}

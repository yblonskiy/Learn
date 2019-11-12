using ApplicationCore.Entities;
using ApplicationCore.Interfaces;
using DayBook.Application.Communication;
using DayBook.Application.Interfaces;
using System;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace DayBook.Application.Services
{
    public class RecordService : IRecordService
    {
        private readonly IRepository<Record> _recordRepository;

        public RecordService(IRepository<Record> recordRepository)
        {
            _recordRepository = recordRepository;
        }
     
        /// <summary>
        /// Return record list by user id
        /// </summary>
        /// <returns></returns>
        public IEnumerable<Record> ListByUserId(string userId)
        {
            return _recordRepository.FindBy(r => r.UserId == userId);
        }

        public async Task<RecordResponse> AddAsync(Record record)
        {
            try
            {
                await _recordRepository.AddAsync(record);

                return new RecordResponse(record);
            }
            catch (Exception ex)
            {
                return new RecordResponse($"An error occurred when saving the record: {ex.Message}");
            }
        }

        public async Task<RecordResponse> UpdateAsync(string id, Record record)
        {
            var existingRecord = await _recordRepository.GetSingleAsync(r => r.Id == id);

            if (existingRecord == null)
                return new RecordResponse("Record not found.");

            existingRecord.Title = record.Title;
            existingRecord.Body = record.Body;

            try
            {
                await _recordRepository.UpdateAsync(existingRecord);

                return new RecordResponse(record);
            }
            catch (Exception ex)
            {
                return new RecordResponse($"An error occurred when updating the record: {ex.Message}");
            }
        }

        public async Task<RecordResponse> DeleteAsync(string id)
        {
            var existingRecord = await _recordRepository.GetSingleAsync(r => r.Id == id);

            if (existingRecord == null)
                return new RecordResponse("Record not found.");

            if (existingRecord.DateCreated < DateTime.Now.AddDays(-2))
            {
                return new RecordResponse("You can't delete this record!");
            }

            try
            {
                await _recordRepository.DeleteAsync(existingRecord);

                return new RecordResponse(existingRecord);
            }
            catch (Exception ex)
            {
                return new RecordResponse($"An error occurred when deleting the record: {ex.Message}");
            }
        }

        /// <summary>
        /// Return record by id and user id
        /// </summary>
        /// <returns></returns>
        public async Task<Record> GetSingleByUserIdAsync(string id, string userId)
        {
            return await _recordRepository.GetSingleAsync(r => r.Id.ToLower() == id.ToLower() && r.UserId == userId);
        }
    }
}

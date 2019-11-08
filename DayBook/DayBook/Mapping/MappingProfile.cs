using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using AutoMapper;
using ApplicationCore.Entities;
using DayBook.Web.ViewModels.Record;

namespace DayBook.Web.Mapping
{
    public class MappingProfile : Profile
    {
        public MappingProfile()
        {
            CreateMap<Record, RecordViewModel>();
            CreateMap<RecordViewModel, Record>();
        }
    }
}

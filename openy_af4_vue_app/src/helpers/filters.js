export function activitiesList(src, activities) {
  let filters = []
  let activityDetails = value => {
    let result = ''
    activities.forEach(activity_type => {
      let activity = activity_type.value.find(item => String(item.value) === String(value))
      if (activity) {
        result = activity_type.label + ' (' + activity.label + ')'
      }
    })
    return result
  }

  src.forEach(item => filters.push(activityDetails(item)))
  return filters.filter(x => x !== '')
}

export function agesList(src, ages) {
  let filters = []
  let agesDetails = value => {
    let result = ''
    let age = ages.find(item => item.value === value)
    if (age) {
      result = age.label
    }
    return result
  }

  src.forEach(item => filters.push(agesDetails(item)))
  return filters.filter(x => x !== '')
}

export function daysList(src, days) {
  let filters = []
  let dayDetails = value => {
    let result = ''
    let day = days.find(item => item.value === value)
    if (day) {
      result = day.search_value.charAt(0).toUpperCase() + day.search_value.slice(1)
    }
    return result
  }

  src.forEach(day => filters.push(dayDetails(day)))
  return filters.filter(x => x !== '')
}

export function timesList(src, times) {
  let filters = []
  let timeDetails = value => {
    let result = ''
    let day = times.find(item => item.value === value)
    if (day) {
      result = day.label
    }
    return result
  }

  src.forEach(time => filters.push(timeDetails(time)))
  return filters.filter(x => x !== '')
}

export function daysTimesList(src, daysTimes) {
  let filters = []
  let daysTimesDetails = value => {
    let result = ''
    daysTimes.forEach(day => {
      let time = day.value.find(item => String(item.value) === String(value))
      if (time) {
        result =
          day.search_value.charAt(0).toUpperCase() +
          day.search_value.slice(1) +
          ' (' +
          time.label +
          ')'
      }
    })
    return result
  }

  src.forEach(item => filters.push(daysTimesDetails(item)))
  return filters.filter(x => x !== '')
}

export function locationsList(src, locations) {
  let filters = []
  let locationsDetails = value => {
    let result = ''
    locations.forEach(location_type => {
      let location = location_type.value.find(item => item.value === value)
      if (location) {
        result = location.label
      }
    })
    return result
  }

  src.forEach(item => filters.push(locationsDetails(item)))
  return filters.filter(x => x !== '')
}

export function weeksList(src, weeks) {
  let filters = []
  let weekDetails = value => {
    let result = ''
    let week = weeks.find(item => item.value === value)
    if (week) {
      result = week.label
    }
    return result
  }

  src.forEach(week => filters.push(weekDetails(week)))
  return filters.filter(x => x !== '')
}

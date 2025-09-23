"use client"

import * as React from "react"
import { format } from "date-fns"
import { Calendar as CalendarIcon, Clock } from "lucide-react"

import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Calendar } from "@/components/ui/calendar"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"

interface DateTimePickerProps {
  date?: Date
  onDateChange?: (date: Date | undefined) => void
  placeholder?: string
  disabled?: boolean
}

export function DateTimePicker({
  date,
  onDateChange,
  placeholder = "Pick a date and time",
  disabled = false,
}: DateTimePickerProps) {
  const [selectedDate, setSelectedDate] = React.useState<Date | undefined>(date)
  const [selectedTime, setSelectedTime] = React.useState<string>("")

  React.useEffect(() => {
    if (date) {
      setSelectedDate(date)
      setSelectedTime(format(date, "HH:mm"))
    }
  }, [date])

  const handleDateSelect = (newDate: Date | undefined) => {
    setSelectedDate(newDate)
    if (newDate && selectedTime) {
      const [hours, minutes] = selectedTime.split(":")
      const dateTime = new Date(newDate)
      dateTime.setHours(parseInt(hours), parseInt(minutes))
      onDateChange?.(dateTime)
    } else {
      onDateChange?.(newDate)
    }
  }

  const handleTimeChange = (time: string) => {
    setSelectedTime(time)
    if (selectedDate && time) {
      const [hours, minutes] = time.split(":")
      const dateTime = new Date(selectedDate)
      dateTime.setHours(parseInt(hours), parseInt(minutes))
      onDateChange?.(dateTime)
    }
  }

  return (
    <div className="flex gap-2">
      <Popover>
        <PopoverTrigger asChild>
          <Button
            variant="outline"
            className={cn(
              "w-full justify-start text-left font-normal",
              !selectedDate && "text-muted-foreground"
            )}
            disabled={disabled}
          >
            <CalendarIcon className="mr-2 h-4 w-4" />
            {selectedDate ? format(selectedDate, "PPP") : <span>{placeholder}</span>}
          </Button>
        </PopoverTrigger>
        <PopoverContent className="w-auto p-0" align="start">
          <Calendar
            mode="single"
            selected={selectedDate}
            onSelect={handleDateSelect}
            disabled={(date) => date < new Date()}
            initialFocus
          />
        </PopoverContent>
      </Popover>
      
      <div className="flex items-center">
        <Clock className="mr-2 h-4 w-4 text-muted-foreground" />
        <input
          type="time"
          value={selectedTime}
          onChange={(e) => handleTimeChange(e.target.value)}
          disabled={disabled || !selectedDate}
          className="px-2 py-1 border border-input rounded-md text-sm focus:ring-2 focus:ring-ring focus:border-transparent"
        />
      </div>
    </div>
  )
}

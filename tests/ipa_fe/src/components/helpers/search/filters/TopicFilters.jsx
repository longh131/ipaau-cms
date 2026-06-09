import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import { TopLevel, ShowAll, FilterItem, FilterWrapper } from './elements'

const TopicFilters = ({ topics, selectedTopics, setSelectedTopics, initialOpen = false }) => {
  const [showAll, setShowAll] = useState(false)
  const [open, setOpen] = useState(initialOpen)

  // Update open state if initialOpen changes (e.g., from URL params)
  useEffect(() => {
    if (initialOpen) {
      setOpen(true)
    }
  }, [initialOpen])

  const handleAccordionToggle = () => {
    if (open) {
      setShowAll(false)
    }
    setOpen(!open)
  }

  const handleTopicChange = (topicName) => {
    setSelectedTopics((prev) => (prev.includes(topicName) ? prev.filter((t) => t !== topicName) : [...prev, topicName]))
  }

  return (
    <>
      {/* Topics Filter */}
      {topics?.length > 0 && (
        <div className="mb-6">
          <TopLevel handleAccordionToggle={handleAccordionToggle} open={open} title="Topics" />
          <FilterWrapper id="topics-accordion" open={open} showAll={showAll}>
            {topics.map((topic, idx) => (
              <>
                {idx === 4 && !showAll && <ShowAll type="topics" setShowAll={setShowAll} open={open} />}
                <FilterItem
                  type="topic"
                  item={topic.name}
                  checked={selectedTopics.includes(topic.name)}
                  onChange={() => handleTopicChange(topic.name)}
                  open={open}
                  idx={idx}
                  showAll={showAll}
                />
              </>
            ))}
          </FilterWrapper>
        </div>
      )}
    </>
  )
}

TopicFilters.propTypes = {
  topics: PropTypes.array,
  selectedTopics: PropTypes.array.isRequired,
  setSelectedTopics: PropTypes.func.isRequired,
  initialOpen: PropTypes.bool,
}

export default TopicFilters

import * as React from "react";

export type Props = {
  seconds: number;
  size: number;
  strokeBgColor: string;
  strokeColor: string;
  strokeWidth: number;
  onTimerEnd?: () => void; // Optional callback prop
};

type State = {
  countdown: number;
  isPlaying: boolean;
};

class Timer extends React.Component<Props, State> {
  milliseconds: number;
  radius: number;
  circumference: number;
  intervalId: NodeJS.Timeout | null;
  strokeDashoffset: () => number;

  constructor(props: Props) {
    super(props);

    this.milliseconds = this.props.seconds * 1000;
    this.radius = this.props.size / 2;
    this.circumference = this.props.size * Math.PI;

    this.state = {
      countdown: this.milliseconds,
      isPlaying: false,
    };

    this.intervalId = null;

    this.strokeDashoffset = () =>
      this.circumference -
      (this.state.countdown / this.milliseconds) * this.circumference;
  }

  componentDidUpdate(prevProps: Props) {
    if (prevProps.seconds !== this.props.seconds) {
      this.milliseconds = this.props.seconds * 1000;
      this.setState({ countdown: this.milliseconds, isPlaying: false });
    }
  }

  startTimer = () => {
    if (this.intervalId) return; // Prevent multiple intervals

    this.setState({ isPlaying: true });

    this.intervalId = setInterval(() => {
      this.setState((prevState) => {
        if (prevState.countdown <= 10) {
          clearInterval(this.intervalId as NodeJS.Timeout);
          this.intervalId = null;
          if (this.props.onTimerEnd) {
            this.props.onTimerEnd();
          }
          return { countdown: 0, isPlaying: false } as Pick<State, 'countdown' | 'isPlaying'>;
        }
        return { countdown: prevState.countdown - 10 } as Pick<State, 'countdown'>;
      });
    }, 10);
  };

  render() {
    const countdownSizeStyles: React.CSSProperties = {
      height: this.props.size,
      width: this.props.size,
    };

    const textStyles: React.CSSProperties = {
      color: this.props.strokeColor,
      fontSize: this.props.size * 0.3,
    };

    const seconds = (this.state.countdown / 1000).toFixed();

    return (
      <div>
        <div
          style={{
            pointerEvents: this.state.isPlaying ? "none" : "all",
            opacity: this.state.isPlaying ? 0.4 : 1,
          }}
        >
        </div>
        <div
          style={{
            ...styles.countdownContainer,
            ...countdownSizeStyles,
          }}
        >
          <p style={textStyles}>{seconds}</p>
          <svg style={styles.svg}>
            <circle
              cx={this.radius}
              cy={this.radius}
              r={this.radius}
              fill="none"
              stroke={this.props.strokeBgColor}
              strokeWidth={this.props.strokeWidth}
            ></circle>
          </svg>
          <svg style={styles.svg}>
            <circle
              strokeDasharray={this.circumference}
              strokeDashoffset={
                this.state.isPlaying ? this.strokeDashoffset() : 0
              }
              r={this.radius}
              cx={this.radius}
              cy={this.radius}
              fill="none"
              strokeLinecap="round"
              stroke={this.props.strokeColor}
              strokeWidth={this.props.strokeWidth}
            ></circle>
          </svg>
        </div>
      </div>
    );
  }
}

const styles = {
  countdownContainer: {
    display: "flex",
    justifyContent: "center",
    alignItems: "center",
    position: "relative",
    margin: "auto",
  } as React.CSSProperties,
  svg: {
    position: "absolute",
    top: 0,
    left: 0,
    width: "100%",
    height: "100%",
    transform: "rotateY(-180deg) rotateZ(-90deg)",
    overflow: "visible",
  } as React.CSSProperties,
  button: {
    fontSize: 16,
    padding: "15px 40px",
    margin: "10px auto 30px",
    display: "block",
    backgroundColor: "#4d4d4d",
    color: "lightgray",
    border: "none",
    cursor: "pointer",
    outline: 0,
  } as React.CSSProperties,
};

export default Timer;

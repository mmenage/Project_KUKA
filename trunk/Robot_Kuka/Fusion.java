package application;


import java.util.ArrayList;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

import com.kuka.generated.ioAccess.MediaFlangeIOGroup;
import com.kuka.roboticsAPI.applicationModel.RoboticsAPIApplication;
import static com.kuka.roboticsAPI.motionModel.BasicMotions.*;

import com.kuka.roboticsAPI.controllerModel.Controller;
import com.kuka.roboticsAPI.controllerModel.sunrise.ISafetyState;
import com.kuka.roboticsAPI.deviceModel.LBR;
import com.kuka.roboticsAPI.deviceModel.OperationMode;
import com.kuka.roboticsAPI.geometricModel.CartDOF;
import com.kuka.roboticsAPI.geometricModel.Frame;
import com.kuka.roboticsAPI.geometricModel.ObjectFrame;
import com.kuka.roboticsAPI.geometricModel.Tool;
import com.kuka.roboticsAPI.geometricModel.math.Transformation;
import com.kuka.roboticsAPI.motionModel.RelativeLIN;
import com.kuka.roboticsAPI.motionModel.Spline;
import com.kuka.roboticsAPI.motionModel.SplineJP;
import com.kuka.roboticsAPI.motionModel.controlModeModel.CartesianSineImpedanceControlMode;


/* Test des mouvements du robot Kuka avec la base "Paper"
 * 
 */

public class Fusion extends RoboticsAPIApplication {
	private Controller kuka_Sunrise_Cabinet_1;
	private LBR lbr_iiwa_14_R820_1;
	private MediaFlangeIOGroup ioFlange;
	private CartesianSineImpedanceControlMode impedanceControlMode;
	private Tool penTool;
	private ObjectFrame penToolTCP;
	private ObjectFrame paperBase;
	private ObjectFrame nearPaper0;
	private ObjectFrame paperApproach;
	private Frame P0;
	private Frame P1;
	private Frame P2;
	private Vector2[] tableau;
	
	
	private Transformation getTranslationWithSpecifiedZ(ObjectFrame frameBefore, Frame p02, double z)
	{
		getLogger().info("Essais 1");
		return Transformation.ofTranslation(
				p02.getX()-frameBefore.getX(), 
				p02.getY()-frameBefore.getY(), 
				z
				);
	}
	
	private Transformation getTranslationFromFrame(Vector2 tableau2, Vector2 tableau3)
	{
		return Transformation.ofTranslation(
				tableau3.x-tableau2.x, 
				tableau3.y-tableau2.y, 
				-1.0
				);
	}
	
	private Transformation getTranslationFromFrame(Frame tableau2, Frame tableau3)
	{
		return Transformation.ofTranslation(
				tableau3.getX()-tableau2.getX(), 
				tableau3.getY()-tableau2.getY(), 
				tableau3.getZ()-tableau2.getZ()
				);
	}
	
	public void initialize() {
		
		// Ligne = new Line(60,140,230,40);
		
		kuka_Sunrise_Cabinet_1 = getController("KUKA_Sunrise_Cabinet_1");
		lbr_iiwa_14_R820_1 = (LBR) getDevice(kuka_Sunrise_Cabinet_1, "LBR_iiwa_14_R820_1");

		ioFlange = new MediaFlangeIOGroup(kuka_Sunrise_Cabinet_1);
	
		// On crée la compliance pour rendre le bras "mou"
		impedanceControlMode = new CartesianSineImpedanceControlMode();
		impedanceControlMode.parametrize(CartDOF.X).setStiffness(3000);
		impedanceControlMode.parametrize(CartDOF.Y).setStiffness(3000);
		impedanceControlMode.parametrize(CartDOF.Z).setStiffness(3000);
		
		impedanceControlMode.parametrize(CartDOF.A).setStiffness(200);
		impedanceControlMode.parametrize(CartDOF.B).setStiffness(200);
		impedanceControlMode.parametrize(CartDOF.C).setStiffness(200);
		
		// On crée l'outil stylo, on l'attache au flange et on récupére le point en bout de stylo "penToolTCP"
		penTool = getApplicationData().createFromTemplate("penTool");
		penTool.attachTo(lbr_iiwa_14_R820_1.getFlange() );
		penToolTCP = penTool.getFrame("/penToolTCP");
		
		
		// On charge les points de l'application
		paperBase = getApplicationData().getFrame("/Paper");
		
		nearPaper0 = getApplicationData().getFrame("/Paper/NearPaper0");
		paperApproach = getApplicationData().getFrame("/Paper/PaperApproach");
		
		getLogger().info("Initialization OK");
	}

	
	public void In_Position(double x, double y){
		
		double velocity = 0.2;
		
		ISafetyState currentState = lbr_iiwa_14_R820_1.getSafetyState();
		OperationMode mode = currentState.getOperationMode();
		if (mode==OperationMode.AUT)
		{
			//******* ATTENTION : AVANT D'ACTIVE LE MODE AUTO, VERIFIER LES TRAJECTOIRES ********//
			// Si on est en mode AUTO, pleine vitesse activée
			
			velocity = 1;
			//velocity = 0.2 ;
		}
		
		P0 = new Frame(paperApproach).setX(x).setY(y).setZ(10);
		P1 = new Frame(x, y, 0);
		penToolTCP.move(
				ptp(paperApproach).setJointVelocityRel(velocity)
			);
		
		penToolTCP.move(
				lin(nearPaper0).setJointVelocityRel(velocity)
			);
		
		getLogger().info("En position");
		penToolTCP.move(
				linRel(getTranslationWithSpecifiedZ(nearPaper0,P0 , P0.getZ() - nearPaper0.getZ()-0.2 ), paperBase)
			);
		
		penToolTCP.move(
				linRel(getTranslationFromFrame(P0,P1), paperBase)
			);
	}	
	
	public void Draw(double x, double y, double x2, double y2){
		
		
		double velocity = 0.2;
		
		ISafetyState currentState = lbr_iiwa_14_R820_1.getSafetyState();
		OperationMode mode = currentState.getOperationMode();
		if (mode==OperationMode.AUT)
		{
			//******* ATTENTION : AVANT D'ACTIVE LE MODE AUTO, VERIFIER LES TRAJECTOIRES ********//
			// Si on est en mode AUTO, pleine vitesse activée
			
			//velocity = 1;
			velocity = 0.2 ;
		}
		
		P1 = new Frame(x, y, -0.2);
		P2 = new Frame(x2, y2, -0.2);
		
		getLogger().info("Je vais en P1");
				
		penToolTCP.move(
				// linRel(20.0,20.0,0.0, paperBase)			
				linRel(getTranslationFromFrame(P1,P2) ,paperBase)
				
			//	linRel(20, 20, 0, paperBase)
			);
		getLogger().info("Fin du trait");
	}
	
	public void Tool_Up(double x, double y){
		
		P0 = new Frame(paperApproach).setX(x).setY(y).setZ(10);
		P1 = new Frame(x, y, 0);
		
		penToolTCP.move(
				linRel(getTranslationFromFrame(P1,P0), paperBase)
			);
	}
	
	
	public void run() {
		
		
		boolean test = true;
		double velocity = 0.2;
		
		ISafetyState currentState = lbr_iiwa_14_R820_1.getSafetyState();
		OperationMode mode = currentState.getOperationMode();
		if (mode==OperationMode.AUT)
		{
			//******* ATTENTION : AVANT D'ACTIVE LE MODE AUTO, VERIFIER LES TRAJECTOIRES ********//
			// Si on est en mode AUTO, pleine vitesse activée
			
			velocity = 1;
			//velocity = 0.2 ;
		}
		
		lbr_iiwa_14_R820_1.move(ptpHome());
		
		// allumer la lumière
		ioFlange.setLEDBlue(true);
		
		// Approche de la base "Paper" en PTP puis en LIN
		
		
		
		// ESSAIS D'UNE BOUCLE DE CONNEXION
		
//		while(test){
		
			getLogger().info("Move near Paper");
			//	
			penToolTCP.move(
					ptp(paperApproach).setJointVelocityRel(velocity)
				);
		
			// connexion au serveur
			
			String message = null;
			// je me connecte au serveur en passant par l'adresse suivante
			Client_ModifV2 C = new Client_ModifV2("192.168.1.10");
			getLogger().info("Le lance le serveur");
			
			//je me met en écoute pour récupérer le message
			message = C.lance();
			
			getLogger().info("Je traite le message");
			
			// on reçoit le tableau de vector2 il s'agit du message reçu en JSON
			tableau = Client_ModifV2.traitement(message);
			
//			RelativeLIN [] splineArray = new RelativeLIN[tableau.length-1];
			getLogger().info("Je caste le message");
			
			
			
/*			
			//je me place à la première position du robot avant de réaliser les mouvements
			In_Position(tableau[0].x, tableau[0].y);
			
			// on stocke le tableau de Vector2 dans un tableau de RelativeLIN pour nous permettre de gérer la vitesse pendant le tracé des lignes.
			for (int i=0; i < tableau.length-1; i++)
			{
			//	getLogger().info("Essais 2");
				RelativeLIN moveLin = linRel(getTranslationFromFrame(tableau[i], tableau[i+1]),paperBase);
			//	getLogger().info("Essais 3");
				splineArray[i] = moveLin;
			//	getLogger().info("Essais 4");
			}
*/
			
			for(int i = 0; i < tableau.length - 1; i++){
				
				System.out.println(tableau.length);
				System.out.println(tableau[i].x);
				System.out.println(tableau[i].y);
			}
			
			
		//	In_Position(tableau[0].x, tableau[0].y);
		//	Draw(tableau[0].x, tableau[0].y, tableau[1].x, tableau[1].y);
			for(int i = 0; i < tableau.length - 1; i+=2){
				
				In_Position(tableau[i].x, tableau[i].y);
				Draw(tableau[i].x, tableau[i].y, tableau[i+1].x, tableau[i+1].y);	
				Tool_Up(tableau[i].x,tableau[i].y );
			}
			
/*			
			Vector2 essais = new Vector2();
			In_Position(tableau[0].x, tableau[0].y);
			Draw(tableau[0].x, tableau[0].y, tableau[1].x, tableau[1].y);
			for(int i =1; i < tableau.length - 2; i++){
				essais.x = tableau[i].x;
				essais.y = tableau[i].y;
				if ((essais.x == tableau[i+1].x)&&(essais.y == tableau[i+1].y)){
					Draw(tableau[i+1].x, tableau[i+1].y, tableau[i+2].x, tableau[i+2].y);
				}else{
					
					Tool_Up(tableau[i+1].x,tableau[i+1].y );
					In_Position(tableau[i+1].x, tableau[i+1].y);
					Draw(tableau[i+1].x, tableau[i+1].y, tableau[i+2].x, tableau[i+2].y);
					
				}
			}
*/			
			getLogger().info("Fin du casting");
			//
//			Spline linMovement = new Spline(splineArray);
			
			// on réalise le mouvement. On y set la vélocité à 20% de la vitesse max du robot.
/*			penToolTCP.move(
					linMovement.setJointVelocityRel(0.2)
				);
			
			getLogger().info("Je vais tracer");
*/
	//	}
		
		// On revient à la "maison"
		getLogger().info("Go back to home");
		
		penToolTCP.move( lin(paperApproach).setJointVelocityRel(velocity));
		
		SplineJP moveBackToHome = new SplineJP( ptpHome());
		
		getLogger().info("Move Back");
		lbr_iiwa_14_R820_1.move(
				moveBackToHome.setJointVelocityRel(velocity)
			);
		
		ioFlange.setLEDBlue(false);
	}

	public static void main(String[] args) {
		Fusion app = new Fusion();
		app.runApplication();
	}
}

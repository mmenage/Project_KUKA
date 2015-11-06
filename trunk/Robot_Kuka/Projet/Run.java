package application;


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
import com.kuka.roboticsAPI.motionModel.SplineJP;
import com.kuka.roboticsAPI.motionModel.controlModeModel.CartesianSineImpedanceControlMode;


/* Test des mouvements du robot Kuka avec la base "Paper"
 * 
 */

public class Run extends RoboticsAPIApplication {
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
	private Vector4[] tab;
	
	private Transformation getTranslationWithSpecifiedZ(ObjectFrame frameBefore, Frame p02, double z)
	{
		getLogger().info("Essais 1");
		return Transformation.ofTranslation(
				p02.getX()-frameBefore.getX(), 
				p02.getY()-frameBefore.getY(), 
				z
				);
	}
	
	// redefinition de la fonction de base en utilisant des Frame au lieu d'ObjectFrame
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

	// fonction pour se placer en position initiale.
	public void In_Position(double x, double y){
		
		double velocity = 0.2;
		
		ISafetyState currentState = lbr_iiwa_14_R820_1.getSafetyState();
		OperationMode mode = currentState.getOperationMode();
		if (mode==OperationMode.AUT)
		{
			//******* ATTENTION : AVANT D'ACTIVE LE MODE AUTO, VERIFIER LES TRAJECTOIRES ********//
			// Si on est en mode AUTO, pleine vitesse activée
			
			velocity = 1;
		}
		
		// on se place en paperApproach
		P0 = new Frame(paperApproach).setX(x).setY(y).setZ(10);
		P1 = new Frame(x, y, 0);
		penToolTCP.move(
				ptp(paperApproach).setJointVelocityRel(velocity)
			);
		// on se place en nearPaper0
		penToolTCP.move(
				lin(nearPaper0).setJointVelocityRel(velocity)
			);
		// on descend le stylo
		getLogger().info("En position");
		penToolTCP.move(
				linRel(getTranslationWithSpecifiedZ(nearPaper0,P0 , P0.getZ() - nearPaper0.getZ()-0.2 ), paperBase)
			);
		
		penToolTCP.move(
				linRel(getTranslationFromFrame(P0,P1), paperBase)
			);
	}	
	
	// fonction pour dessiner
	public void Draw(double x, double y, double x2, double y2){
		
		double velocity;
		ISafetyState currentState = lbr_iiwa_14_R820_1.getSafetyState();
		OperationMode mode = currentState.getOperationMode();
		if (mode==OperationMode.AUT)
		{
			//******* ATTENTION : AVANT D'ACTIVE LE MODE AUTO, VERIFIER LES TRAJECTOIRES ********//
			// Si on est en mode AUTO, pleine vitesse activée
			
			velocity = 0.2 ;
		}
		
		P1 = new Frame(x, y, -0.2);
		P2 = new Frame(x2, y2, -0.2);
		
		getLogger().info("Je vais en P1");
				
		penToolTCP.move(
				// linRel(20.0,20.0,0.0, paperBase)			
				linRel(getTranslationFromFrame(P1,P2) ,paperBase)
				
			);
		getLogger().info("Fin du trait");
	}
	// fonction pour lever le stylo
	public void Tool_Up(double x, double y){
		
		P0 = new Frame(paperApproach).setX(x).setY(y).setZ(10);
		P1 = new Frame(x, y, 0);
		
		penToolTCP.move(
				linRel(getTranslationFromFrame(P1,P0), paperBase)
			);
	}
	// fonction pour bouger d'un point à un autre
	public void Move(double x, double y, double a, double b){
		
		
		P0 = new Frame(x,y,10);
		P1 = new Frame(a,b, 10);
		
		penToolTCP.move(
				linRel(getTranslationFromFrame(P0,P1), paperBase)
			);
	}
	
	// fonction pour le lever le stylo
	public void Tool_Down(double x, double y){
		
		P0 = new Frame(nearPaper0).setX(x).setY(y).setZ(10);
		P1 = new Frame(x, y, 0);

		penToolTCP.move(
				linRel(getTranslationFromFrame(P0,P1), paperBase)
			);
	}
	
	// le run permet de lancer le programme
	public void run() {
		
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
		
		// ESSAIS D'UNE BOUCLE DE CONNEXION
		
			getLogger().info("Move near Paper");
			//	
			penToolTCP.move(
					ptp(paperApproach).setJointVelocityRel(velocity)
				);
		
			// connexion au serveur
			
			String message = null;
			// je me connecte au serveur en passant par l'adresse suivante
			Client C = new Client("192.168.1.10");
			getLogger().info("Le lance le serveur");
			
			//je me met en écoute pour récupérer le message
			message = C.lance();
			
			getLogger().info("Je traite le message");
			
			// on reçoit le tableau de vector2 il s'agit du message reçu en JSON
			tableau = Client.traitement(message);
			// on fabrique un tableau de type Vector4
			tab = new Vector4[tableau.length/2];

			int l = 0;
			// boucle pour remplir le tableau de Vector4
			for (int j = 0; j < tab.length; j++){
				tab[j] = new Vector4(tableau[l].x,tableau[l].y,tableau[l+1].x,tableau[l+1].y);
				l+=2;
			}

			int i = 0;
			int z = 2;
			boolean first = true;
			
		  // fonction pour tracer 
				for (i =0; i < tab.length  ; i++){
					// on test si c'est le premier passage dans la boucle
					if (first == true){
						// on se place et on dessine 
						In_Position(tab[i].x, tab[i].y);
						Draw(tab[i].x, tab[i].y, tab[i].a, tab[i].b);
						first = false;
							
					}else {
						// on compare les valeurs des coordonnées pour savoir si on se trouve sur le même point 
						if ((tab[i-1].a == tableau[z].x) && (tab[i-1].b == tableau[z].y)){
							// on reste au même endroit et on dessine
							System.out.println("je dessine");
							Draw(tab[i].x, tab[i].y, tab[i].a, tab[i].b);
								
						}else {
							// on relève le stylo, on se place à la nouvelle position, on descend le stylo et on dessine
							
							Tool_Up(tab[i].x,tab[i].y);
							
							Move(tab[i-1].a, tab[i-1].b, tab[i].x,tab[i].y);
							
							Tool_Down(tab[i].x,tab[i].y);
							
							Draw(tab[i].x, tab[i].y, tab[i].a, tab[i].b);
						}
						z+=2;
					}	
				}
			
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
		Run app = new Run();
		app.runApplication();
	}
}

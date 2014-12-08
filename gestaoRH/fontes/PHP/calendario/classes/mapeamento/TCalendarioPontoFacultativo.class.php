<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
  * Classe de mapeamento da tabela CALENDARIO_PONTO_FACULTATIVO
  * Data de Criação: 11/04/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Renan O. C. Ferreira - CNM

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.02.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCalendarioPontoFacultativo extends Persistente
{

  /**
    * Método Construtor
    * @access Private
  */
  public function TCalendarioPontoFacultativo()
  {
    parent::Persistente();
    $this->setTabela('calendario.ponto_facultativo');

    $this->setCampoCod('cod_feriado');
    $this->setComplementoChave('');

    $this->AddCampo('cod_feriado','integer',true,'',true,true);
  }

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                                    \n";
    $stSQL .= "     F.cod_feriado,                                        \n";
    $stSQL .= "     to_char (F.dt_feriado, 'dd/mm/yyyy') as dt_feriado,   \n";
    $stSQL .= "     F.descricao,                                          \n";
    $stSQL .= "  CASE tipoferiado                                         \n";
    $stSQL .= "    WHEN 'P' THEN 'Ponto facultativo'                      \n";
    $stSQL .= "  END as tipoferiado                                       \n";
    $stSQL .= " FROM                                                      \n";
    $stSQL .= "   calendario.feriado AS F,                            \n";
    $stSQL .= "   calendario.ponto_facultativo AS PF                  \n";
    $stSQL .= " WHERE                                                     \n";
    $stSQL .= "     F.cod_feriado = PF.cod_feriado  and                   \n";
    $stSQL .= "     F.tipoferiado = 'P'                                   \n";

    return $stSQL;
}

}

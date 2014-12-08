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
  * Classe de mapeamento da tabela CONCURSO.ATRIBUTO_CANDIDATO_VALOR
  * Data de Criação: 29/03/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_ATRIBUTOS_VALORES );
include_once ( CAM_GRH_CON_MAPEAMENTO."TConcursoAtributoConcurso.class.php"        );

/**
  * Efetua conexão com a tabela  CONCURSO.ATRIBUTO_CANDIDATO_VALOR
  * Data de Criação: 29/03/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  $Revision: 30566 $
  $Name$
  $Author: souzadl $
  $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

  * Casos de uso: uc-00.00.00

*/
class TConcursoAtributoCandidatoValor extends PersistenteAtributosValores
{
/**
    * Método Construtor
    * @access Private
*/
function TConcursoAtributoCandidatoValor()
{
    parent::PersistenteAtributosValores();
    $this->setTabela('concurso.atributo_candidato_valor');
    $this->setPersistenteAtributo( new TConcursoAtributoConcurso );

    $this->setCampoCod('');
    $this->setComplementoChave('cod_atributo,cod_cadastro,cod_candidato');

    $this->AddCampo('cod_atributo','integer',true,'',true,true);
    $this->AddCampo('cod_cadastro','integer',true,'',true,true);
    $this->AddCampo('cod_candidato','integer',true,'',true,true);
    $this->AddCampo('valor','varchar',true,'100',false,false);

}
}
